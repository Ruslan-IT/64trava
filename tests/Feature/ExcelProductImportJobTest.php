<?php

namespace Tests\Feature;

use App\Jobs\ImportProductsFromExcelJob;
use App\Services\ExcelProductImportService;
use Illuminate\Support\Facades\Storage;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class ExcelProductImportJobTest extends TestCase
{
    public function test_job_imports_the_stored_file_and_deletes_it(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('imports/catalog.xlsx', 'xlsx');

        $mock = Mockery::mock(ExcelProductImportService::class);
        $mock->shouldReceive('import')
            ->once()
            ->withArgs(function (string $file, string $brand): bool {
                return $file === Storage::disk('local')->path('imports/catalog.xlsx')
                    && is_file($file)
                    && $brand === "Barney's Farm";
            })
            ->andReturn([
                'imported' => 1,
                'updated' => 0,
                'skipped' => 0,
                'errors' => [],
                'warnings' => [],
            ]);
        $this->app->instance(ExcelProductImportService::class, $mock);

        (new ImportProductsFromExcelJob('imports/catalog.xlsx', "Barney's Farm"))
            ->handle($this->app->make(ExcelProductImportService::class));

        Storage::disk('local')->assertMissing('imports/catalog.xlsx');
    }

    public function test_job_keeps_the_file_when_import_fails(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('imports/catalog.xlsx', 'xlsx');

        $mock = Mockery::mock(ExcelProductImportService::class);
        $mock->shouldReceive('import')->once()->andThrow(new RuntimeException('Файл повреждён'));
        $this->app->instance(ExcelProductImportService::class, $mock);

        try {
            (new ImportProductsFromExcelJob('imports/catalog.xlsx', ''))
                ->handle($this->app->make(ExcelProductImportService::class));
            $this->fail('Исключение импорта должно всплыть из Job.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Файл повреждён', $exception->getMessage());
        }

        Storage::disk('local')->assertExists('imports/catalog.xlsx');
        $this->assertSame(3, (new ImportProductsFromExcelJob('imports/catalog.xlsx'))->tries);
    }

    public function test_job_fails_when_the_file_is_missing_and_does_not_import(): void
    {
        Storage::fake('local');

        $mock = Mockery::mock(ExcelProductImportService::class);
        $mock->shouldNotReceive('import');
        $this->app->instance(ExcelProductImportService::class, $mock);

        $this->expectException(RuntimeException::class);

        (new ImportProductsFromExcelJob('imports/missing.xlsx', ''))
            ->handle($this->app->make(ExcelProductImportService::class));
    }
}
