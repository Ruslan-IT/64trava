<?php

namespace Tests\Feature;

use App\Filament\Resources\Products\Pages\ListProducts;
use App\Jobs\ImportProductsFromExcelJob;
use App\Models\Brand;
use App\Models\User;
use App\Services\ExcelProductImportService;
use Filament\Actions\Action;
use Filament\Notifications\Livewire\Notifications;
use Filament\Notifications\Notification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Mockery;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ExcelProductImportFilamentTest extends TestCase
{
    use DatabaseTransactions;

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

    public function test_products_list_has_excel_import_action(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(ListProducts::class)
            ->assertActionExists('importExcel', function (Action $action): bool {
                return $action->getLabel() === 'Импорт из Excel'
                    && $action->getModalHeading() === 'Импорт товаров из Excel'
                    && $action->getModalSubmitActionLabel() === 'Импортировать';
            });
    }

    public function test_import_action_queues_the_file_without_importing_it(): void
    {
        $this->actingAs(User::factory()->create());
        Queue::fake();
        Storage::fake('local');

        $mock = Mockery::mock(ExcelProductImportService::class);
        $mock->shouldNotReceive('import');
        $this->app->instance(ExcelProductImportService::class, $mock);

        Livewire::test(ListProducts::class)
            ->callAction('importExcel', [
                'file' => $this->xlsxUpload(),
            ])
            ->assertHasNoFormErrors();

        $notification = $this->sentNotification();

        $this->assertSame('Импорт поставлен в очередь', $notification->getTitle());
        $this->assertStringContainsString('выполняется в фоне', (string) $notification->getBody());

        Queue::assertPushed(ImportProductsFromExcelJob::class, function (ImportProductsFromExcelJob $job): bool {
            return $job->brand === '' && Storage::disk('local')->exists($job->path);
        });
    }

    public function test_selected_brand_is_passed_as_fallback(): void
    {
        $this->actingAs(User::factory()->create());
        Brand::query()->firstOrCreate(['name' => "Barney's Farm"]);
        Queue::fake();
        Storage::fake('local');

        $mock = Mockery::mock(ExcelProductImportService::class);
        $mock->shouldNotReceive('import');
        $this->app->instance(ExcelProductImportService::class, $mock);

        Livewire::test(ListProducts::class)
            ->mountAction('importExcel')
            ->fillForm([
                'file' => $this->xlsxUpload(),
            ])
            ->set('mountedActions.0.data.brand', "Barney's Farm")
            ->callMountedAction()
            ->assertHasNoFormErrors();

        Queue::assertPushed(ImportProductsFromExcelJob::class, function (ImportProductsFromExcelJob $job): bool {
            return $job->brand === "Barney's Farm" && Storage::disk('local')->exists($job->path);
        });
    }

    public function test_queued_excel_file_is_kept_until_the_job_runs(): void
    {
        $this->actingAs(User::factory()->create());
        Queue::fake();
        Storage::fake('local');

        Livewire::test(ListProducts::class)
            ->callAction('importExcel', [
                'file' => $this->xlsxUpload(),
            ])
            ->assertHasNoFormErrors();

        Queue::assertPushed(ImportProductsFromExcelJob::class, function (ImportProductsFromExcelJob $job): bool {
            return Storage::disk('local')->exists($job->path);
        });
    }

    public function test_non_xlsx_file_is_rejected(): void
    {
        $this->actingAs(User::factory()->create());
        Queue::fake();

        $mock = Mockery::mock(ExcelProductImportService::class);
        $mock->shouldNotReceive('import');
        $this->app->instance(ExcelProductImportService::class, $mock);

        Livewire::test(ListProducts::class)
            ->callAction('importExcel', [
                'file' => UploadedFile::fake()->create('notes.txt', 1, 'text/plain'),
            ])
            ->assertHasFormErrors(['file']);

        Queue::assertNotPushed(ImportProductsFromExcelJob::class);
        Notification::assertNotNotified('Импорт поставлен в очередь');
    }

    private function xlsxUpload(): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'xlsx');
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->setCellValue('A1', 'Name');
        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        $upload = UploadedFile::fake()->createWithContent(
            'BarneysEtalon_0.xlsx',
            (string) file_get_contents($path),
        );

        @unlink($path);

        return $upload;
    }

    private function sentNotification(): Notification
    {
        $component = new Notifications;
        $component->mount();

        $notification = $component->notifications->first();
        $this->assertInstanceOf(Notification::class, $notification);

        return $notification;
    }
}
