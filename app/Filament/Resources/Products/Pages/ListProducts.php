<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Jobs\ImportProductsFromExcelJob;
use App\Models\Product;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->trashAction(),
            $this->lastImportReportAction(),
            $this->importExcelAction(),
            CreateAction::make(),
        ];
    }

    private function trashAction(): Action
    {
        return Action::make('trash')
            ->label(fn (): string => 'Корзина ('.$this->trashedProductsCount().')')
            ->icon(Heroicon::OutlinedTrash)
            ->color('gray')
            ->url(ProductResource::getUrl('trash'));
    }

    private function trashedProductsCount(): int
    {
        return Product::onlyTrashed()->count();
    }

    private function lastImportReportAction(): Action
    {
        return Action::make('lastImportReport')
            ->label('Последний отчёт импорта')
            ->icon(Heroicon::OutlinedDocumentText)
            ->color('gray')
            ->modalHeading('Последний отчёт импорта Excel')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Закрыть')
            ->disabled(fn (): bool => $this->latestImportReport() === null)
            ->tooltip(fn (): ?string => $this->latestImportReport() === null ? 'Отчётов импорта пока нет' : null)
            ->modalContent(function (): \Illuminate\Support\HtmlString {
                $report = $this->latestImportReport();

                if ($report === null) {
                    return new \Illuminate\Support\HtmlString('<p>Отчётов импорта пока нет.</p>');
                }

                $errors = collect($report['data']['errors'] ?? [])->take(10)
                    ->map(fn (array $error): string => e((string) ($error['message'] ?? json_encode($error, JSON_UNESCAPED_UNICODE))))
                    ->implode('<br>');

                $html = '<p><strong>Файл:</strong> '.e((string) ($report['data']['file'] ?? '—')).'</p>'
                    .'<p><strong>Завершён:</strong> '.e((string) ($report['data']['finished_at'] ?? '—')).'</p>'
                    .'<p><strong>Создано:</strong> '.(int) ($report['data']['imported'] ?? 0).'</p>'
                    .'<p><strong>Обновлено:</strong> '.(int) ($report['data']['updated'] ?? 0).'</p>'
                    .'<p><strong>Пропущено:</strong> '.(int) ($report['data']['skipped'] ?? 0).'</p>'
                    .'<p><strong>Ошибок:</strong> '.count($report['data']['errors'] ?? []).'</p>'
                    .'<p><strong>Предупреждений:</strong> '.count($report['data']['warnings'] ?? []).'</p>';

                if ($errors !== '') {
                    $html .= '<p><strong>Примеры ошибок:</strong><br>'.$errors.'</p>';
                }

                $html .= '<p class="text-sm text-gray-500">Отчёт: '.e($report['path']).'</p>';

                return new \Illuminate\Support\HtmlString($html);
            });
    }

    /**
     * @return array{path: string, data: array<string, mixed>}|null
     */
    private function latestImportReport(): ?array
    {
        $files = collect(Storage::disk('local')->files('imports/reports'))
            ->filter(fn (string $path): bool => str_ends_with($path, '.json'))
            ->sortByDesc(fn (string $path): int => Storage::disk('local')->lastModified($path))
            ->values();

        $path = $files->first();

        if (! is_string($path)) {
            return null;
        }

        $decoded = json_decode((string) Storage::disk('local')->get($path), true);

        if (! is_array($decoded)) {
            return null;
        }

        return [
            'path' => $path,
            'data' => $decoded,
        ];
    }

    private function importExcelAction(): Action
    {
        return Action::make('importExcel')
            ->label('Импорт из Excel')
            ->icon(Heroicon::OutlinedArrowUpTray)
            ->modalHeading('Импорт товаров из Excel')
            ->modalSubmitActionLabel('Импортировать')
            ->modalCancelActionLabel('Отмена')
            ->successNotification(null)
            ->disabled(fn (): bool => $this->excelImportIsQueued())
            ->tooltip(fn (): ?string => $this->excelImportIsQueued() ? 'Импорт уже выполняется' : null)
            ->schema([
                FileUpload::make('file')
                    ->label('Excel-файл')
                    ->required()
                    ->storeFiles(false)
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ])
                    ->mimeTypeMap([
                        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ])
                    ->helperText('Только .xlsx.')
                    ->rule(static function (): Closure {
                        return static function (string $attribute, mixed $value, Closure $fail): void {
                            if (! $value instanceof TemporaryUploadedFile) {
                                $fail('Выберите файл .xlsx.');

                                return;
                            }

                            if (strtolower($value->getClientOriginalExtension()) !== 'xlsx') {
                                $fail('Допустим только файл .xlsx.');

                                return;
                            }

                            $path = $value->getRealPath();

                            if (! is_string($path) || ! is_file($path) || ! is_readable($path)) {
                                $fail('Файл не найден или его нельзя прочитать.');
                            }
                        };
                    }),
            ])
            ->action(function (array $data, Action $action): void {
                $uploaded = $data['file'] ?? null;

                if (! $uploaded instanceof TemporaryUploadedFile) {
                    throw ValidationException::withMessages([
                        'mountedActions.'.$action->getNestingIndex().'.data.file' => 'Выберите файл .xlsx.',
                    ]);
                }

                $path = $uploaded->getRealPath();

                if (! is_string($path) || ! is_file($path) || ! is_readable($path)) {
                    $uploaded->delete();

                    throw ValidationException::withMessages([
                        'mountedActions.'.$action->getNestingIndex().'.data.file' => 'Файл не найден или его нельзя прочитать.',
                    ]);
                }

                $storedPath = $uploaded->storeAs('imports', Str::uuid()->toString().'.xlsx');

                if (! is_string($storedPath) || ! Storage::disk('local')->exists($storedPath)) {
                    throw ValidationException::withMessages([
                        'mountedActions.'.$action->getNestingIndex().'.data.file' => 'Не удалось сохранить Excel-файл.',
                    ]);
                }

                $uploaded->delete();

                ImportProductsFromExcelJob::dispatch($storedPath, '');

                $this->flushCachedTableRecords();

                Notification::make()
                    ->title('Импорт поставлен в очередь')
                    ->body('Файл принят. Импорт товаров и изображений выполняется в фоне.')
                    ->success()
                    ->send();
            });
    }

    private function excelImportIsQueued(): bool
    {
        return DB::table('jobs')
            ->where('payload', 'like', '%ImportProductsFromExcelJob%')
            ->exists();
    }
}
