<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Jobs\ImportProductsFromExcelJob;
use App\Models\Brand;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
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
            $this->importExcelAction(),
            CreateAction::make(),
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
                Select::make('brand')
                    ->label('Бренд (необязательно)')
                    ->placeholder('Выберите бренд')
                    ->options(fn (): array => Brand::query()->orderBy('name')->pluck('name', 'name')->all())
                    ->searchable()
                    ->preload()
                    ->helperText('Используется только если колонка Brand в Excel пустая.'),
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

                $brand = trim((string) ($data['brand'] ?? ''));
                $storedPath = $uploaded->storeAs('imports', Str::uuid()->toString().'.xlsx');

                if (! is_string($storedPath) || ! Storage::disk('local')->exists($storedPath)) {
                    throw ValidationException::withMessages([
                        'mountedActions.'.$action->getNestingIndex().'.data.file' => 'Не удалось сохранить Excel-файл.',
                    ]);
                }

                $uploaded->delete();

                ImportProductsFromExcelJob::dispatch($storedPath, $brand);

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
