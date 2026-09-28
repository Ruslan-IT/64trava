<?php

namespace Tests\Feature;

use App\Jobs\ImportProductsFromExcelJob;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TelegramProductImportTest extends TestCase
{
    use DatabaseTransactions;

    private string $fileBody = "Name,Type,Brand\nA,Feminised,Farm\n";

    public function createApplication()
    {
        $app = parent::createApplication();

        $mysql = $app['config']->get('database.connections.mysql');
        $mysql['url'] = null;
        $mysql['host'] = 'MySQL-8.0';
        $mysql['port'] = '3306';
        $mysql['database'] = '64trava_testing';
        $mysql['username'] = 'root';
        $mysql['password'] = '';

        $app['config']->set('database.connections.mysql', $mysql);
        $app['config']->set('database.default', 'mysql');

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.telegram.bot_token' => 'test-token',
            'services.telegram.manager_chat_id' => '955',
            'services.telegram.import_user_ids' => '1001',
            'services.telegram.webhook_secret' => 'secret',
        ]);
        Storage::fake('local');
        Queue::fake();
        DB::table('jobs')->where('payload', 'like', '%ImportProductsFromExcelJob%')->delete();
        Http::fake(function ($request) {
            $url = $request->url();

            if (str_contains($url, 'getFile')) {
                return Http::response([
                    'ok' => true,
                    'result' => ['file_path' => 'documents/products.csv'],
                ]);
            }

            if (str_contains($url, '/file/bot')) {
                return Http::response($this->fileBody);
            }

            return Http::response(['ok' => true]);
        });
    }

    public function test_allowed_user_queues_the_existing_import(): void
    {
        $this->postWebhook(955, 'products.csv')->assertOk();

        Queue::assertPushed(ImportProductsFromExcelJob::class, function (ImportProductsFromExcelJob $job) {
            return str_ends_with($job->path, '.csv') && $job->brand === '';
        });
        $this->assertNotEmpty(Storage::disk('local')->allFiles('imports'));
        Http::assertSent(fn ($request) => $this->messageContains($request, 'Импорт поставлен в очередь'));
    }

    public function test_unknown_user_cannot_import(): void
    {
        $this->postWebhook(42, 'products.csv')->assertOk();

        Queue::assertNothingPushed();
        Http::assertSent(fn ($request) => $this->messageContains($request, 'Недостаточно прав'));
        $this->assertSame([], Storage::disk('local')->allFiles('imports'));
    }

    public function test_wrong_webhook_secret_is_rejected(): void
    {
        $this->postJson(route('telegram.webhook'), $this->payload(955, 'products.csv'), [
            'X-Telegram-Bot-Api-Secret-Token' => 'wrong',
        ])->assertForbidden();

        Queue::assertNothingPushed();
    }

    public function test_second_import_is_rejected_while_one_is_queued(): void
    {
        DB::table('jobs')->insert([
            'queue' => 'default',
            'payload' => json_encode(['displayName' => 'App\\Jobs\\ImportProductsFromExcelJob']),
            'attempts' => 0,
            'available_at' => time(),
            'created_at' => time(),
        ]);

        $this->postWebhook(955, 'products.csv')->assertOk();

        Queue::assertNothingPushed();
        Http::assertSent(fn ($request) => $this->messageContains($request, 'Импорт уже выполняется'));
    }

    public function test_invalid_csv_is_not_imported(): void
    {
        $this->fileBody = 'это не таблица';

        $this->postWebhook(1001, 'note.csv')->assertOk();

        Queue::assertNothingPushed();
        Http::assertSent(fn ($request) => $this->messageContains($request, 'не похож на допустимый'));
    }

    private function messageContains($request, string $fragment): bool
    {
        if (! str_contains($request->url(), 'sendMessage')) {
            return false;
        }

        return str_contains((string) ($request->data()['text'] ?? ''), $fragment);
    }

    private function postWebhook(int $userId, string $fileName)
    {
        return $this->postJson(route('telegram.webhook'), $this->payload($userId, $fileName), [
            'X-Telegram-Bot-Api-Secret-Token' => 'secret',
        ]);
    }

    private function payload(int $userId, string $fileName): array
    {
        return [
            'message' => [
                'from' => ['id' => $userId],
                'chat' => ['id' => $userId],
                'document' => [
                    'file_id' => 'file-1',
                    'file_name' => $fileName,
                ],
            ],
        ];
    }
}
