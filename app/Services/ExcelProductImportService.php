<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use RuntimeException;

class ExcelProductImportService
{
    private const TYPE_MAP = [
        'Feminised' => 'F',
        'Autoflower' => 'A',
        'Regular' => 'R',
        'Autoregular' => 'AR',
    ];

    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    public function __construct(private ProductImportService $products) {}

    public function import(string $file, string $brand = ''): array
    {
        $fallbackBrand = trim($brand);

        $worksheet = $this->loadSpreadsheet($file)->getActiveSheet();
        $lastRow = max(1, (int) $worksheet->getHighestDataRow());
        $lastColumn = $worksheet->getHighestDataColumn() ?: 'A';
        $sheet = $worksheet->rangeToArray('A1:'.$lastColumn.$lastRow, null, true, true, false);
        $headers = $this->headers($sheet[0] ?? []);

        $result = [
            'imported' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => [],
            'warnings' => [],
            'photos' => [],
            'photo_retry' => [],
        ];

        foreach (array_slice($sheet, 1) as $offset => $cells) {
            $excelRow = $offset + 2;

            if ($this->isEmptyRow($cells)) {
                $result['skipped']++;
                continue;
            }

            $name = $this->text($this->cell($cells, $headers, 'Name'));

            if ($name === null) {
                $result['skipped']++;
                continue;
            }

            $type = $this->text($this->cell($cells, $headers, 'Type'));

            if ($type === null) {
                $result['errors'][] = [
                    'row' => $excelRow,
                    'field' => 'Type',
                    'message' => 'Строка Excel '.$excelRow.': не указан Type',
                ];
                continue;
            }

            if (! isset(self::TYPE_MAP[$type])) {
                $result['errors'][] = [
                    'row' => $excelRow,
                    'field' => 'Type',
                    'message' => 'Неизвестный тип семян',
                ];
                continue;
            }

            $this->warnUnmapped($result, $excelRow, $cells, $headers, 'Fas Q');
            $this->warnUnmapped($result, $excelRow, $cells, $headers, 'Dis1');
            $this->warnUnmapped($result, $excelRow, $cells, $headers, 'Dis2');

            $brandName = $this->resolveBrand($cells, $headers, $fallbackBrand);

            if ($brandName === null) {
                $result['errors'][] = $this->brandError($excelRow, $headers);
                continue;
            }

            $variants = $this->variants($cells, $headers);
            $price = $variants[0]['price'] ?? null;

            if ($variants === [] || $price === null) {
                $variants = [];
                $price = null;
                $result['warnings'][] = [
                    'row' => $excelRow,
                    'field' => 'Price1',
                    'message' => 'Не указана цена — варианты товара не созданы',
                ];
            }

            $payload = [
                'strain' => $name,
                'brand' => $brandName,
                'seed_type' => self::TYPE_MAP[$type],
                'price' => $price,
                'thc' => $this->thc($this->cell($cells, $headers, 'THC %')),
                'cbd' => $this->cbd($this->cell($cells, $headers, 'CBD %')),
                'sativa_percent' => $this->percent($this->cell($cells, $headers, 'Sativa %')),
                'indica_percent' => $this->percent($this->cell($cells, $headers, 'Indica %')),
                'taste' => $this->text($this->cell($cells, $headers, 'Вкус')),
                'effect' => $this->text($this->cell($cells, $headers, 'Эффект')),
                'aroma' => $this->text($this->cell($cells, $headers, 'Аромат')),
                'indoor_height' => $this->text($this->cell($cells, $headers, 'Height Indoor (cm)')),
                'yield' => $this->text($this->cell($cells, $headers, 'Indoor Yield (g/m²)')),
                'flowering' => $this->text($this->cell($cells, $headers, 'Flowering Time (days)')),
                'height' => $this->text($this->cell($cells, $headers, 'Height Outdoor (cm)')),
                'outdoor_yield' => $this->text($this->cell($cells, $headers, 'Outdoor Yield (g/plant)')),
                'harvest' => $this->text($this->cell($cells, $headers, 'Harvest')),
                'variants' => $variants,
            ];

            Log::info('Excel import: start row', [
                'row' => $excelRow,
                'strain' => $payload['strain'] ?? null,
                'brand' => $payload['brand'] ?? null,
            ]);

            $countBefore = Product::query()->count();

            try {
                $product = $this->products->import($payload);
            } catch (ValidationException $exception) {
                $result['errors'][] = [
                    'row' => $excelRow,
                    'field' => (string) array_key_first($exception->errors()),
                    'message' => collect($exception->errors())->flatten()->first() ?: 'Строка не прошла проверку',
                ];
                continue;
            }

            if (Product::query()->count() > $countBefore) {
                $result['imported']++;
            } else {
                $result['updated']++;
            }

            $product->genetics = $this->text($this->cell($cells, $headers, 'Genetics'));
            $product->description = $this->text($this->cell($cells, $headers, 'TXT1'));
            $product->full_description = $this->text($this->cell($cells, $headers, 'TXT2'));
            $product->excel_row = $excelRow;
            $visibility = $this->catalogVisibility($cells, $headers);

            if ($visibility !== null) {
                $product->is_visible = $visibility;
            }

            $product->save();

            $this->attachImages($product, $cells, $headers, $excelRow, $result);

            Log::info('Excel import: product completed', [
                'row' => $excelRow,
                'strain' => $payload['strain'] ?? null,
            ]);
        }

        return $result;
    }

    private function resolveBrand(array $cells, array $headers, string $fallbackBrand): ?string
    {
        if (array_key_exists('Brand', $headers)) {
            $fromColumn = $this->text($this->cell($cells, $headers, 'Brand'));

            if ($fromColumn !== null) {
                return $fromColumn;
            }
        }

        return $fallbackBrand !== '' ? $fallbackBrand : null;
    }

    private function loadSpreadsheet(string $file): Spreadsheet
    {
        if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) !== 'csv') {
            return IOFactory::load($file);
        }

        $reader = IOFactory::createReader('Csv');
        $reader->setInputEncoding('UTF-8');
        $reader->setDelimiter($this->csvDelimiter($file));
        $reader->setEnclosure('"');

        return $reader->load($file);
    }

    private function csvDelimiter(string $file): string
    {
        $line = '';
        $handle = fopen($file, 'rb');

        if ($handle !== false) {
            $line = (string) fgets($handle);
            fclose($handle);
        }

        $line = preg_replace('/^\xEF\xBB\xBF/', '', $line) ?? $line;
        $counts = [
            ',' => substr_count($line, ','),
            ';' => substr_count($line, ';'),
            "\t" => substr_count($line, "\t"),
        ];
        arsort($counts);
        $delimiter = (string) array_key_first($counts);

        return ($counts[$delimiter] ?? 0) > 0 ? $delimiter : ',';
    }

    /**
     * @param  array<int, mixed>  $cells
     * @param  array<string, int>  $headers
     */
    private function catalogVisibility(array $cells, array $headers): ?bool
    {
        foreach (['Показывать в каталоге', 'is_visible'] as $column) {
            if (! array_key_exists($column, $headers)) {
                continue;
            }

            $value = $this->text($this->cell($cells, $headers, $column));

            if ($value === null) {
                return null;
            }

            $normalized = mb_strtolower($value);

            if (in_array($normalized, ['1', 'да', 'yes', 'true', 'вкл'], true)) {
                return true;
            }

            if (in_array($normalized, ['0', 'нет', 'no', 'false', 'выкл'], true)) {
                return false;
            }

            return null;
        }

        return null;
    }

    private function brandError(int $excelRow, array $headers): array
    {
        $brandColumnExists = array_key_exists('Brand', $headers);

        return [
            'row' => $excelRow,
            'field' => 'Brand',
            'message' => $brandColumnExists
                ? 'Строка '.$excelRow.': не удалось определить бренд — колонка Brand пустая'
                : 'Строка '.$excelRow.': не удалось определить бренд',
        ];
    }

    private function headers(array $row): array
    {
        $headers = [];

        foreach ($row as $index => $header) {
            $header = $this->text($header);

            if ($header !== null) {
                $headers[$header] = $index;
            }
        }

        return $headers;
    }

    private function variants(array $cells, array $headers): array
    {
        $variants = [];

        foreach ([1, 2] as $index) {
            $package = $this->cell($cells, $headers, 'Fas'.$index);
            $sku = $this->text($this->cell($cells, $headers, 'Art'.$index));
            $price = $this->cell($cells, $headers, 'Price'.$index);

            if ($this->text($package) === null && $sku === null && $this->text($price) === null) {
                continue;
            }

            $variants[] = [
                'package_size' => is_numeric($package) ? (int) $package : $package,
                'sku' => $sku ?? '',
                'price' => is_numeric($price) ? $price + 0 : $price,
                'stock' => 0,
            ];
        }

        return $variants;
    }

    /**
     * @param  array<int, array<string, mixed>>  $photos
     * @return array{photos: array<int, array<string, mixed>>, photo_retry: array<int, array<string, mixed>>, errors: array<int, array<string, mixed>>}
     */
    public function retryProductImages(array $photos): array
    {
        $errors = [];
        $byProduct = [];

        foreach ($photos as $photo) {
            if (! isset($photo['product_id'])) {
                continue;
            }

            $byProduct[$photo['product_id']][] = $photo;
        }

        $updated = [];

        foreach ($byProduct as $productId => $slots) {
            $product = Product::query()->find($productId);

            if ($product === null) {
                continue;
            }

            usort($slots, fn (array $left, array $right): int => $this->fotoIndex((string) ($left['field'] ?? '')) <=> $this->fotoIndex((string) ($right['field'] ?? '')));

            foreach ($slots as $index => $slot) {
                $url = (string) ($slot['url'] ?? '');
                $stored = $this->storedImagePath($url);

                if ($stored !== null) {
                    $slots[$index]['retryable'] = false;
                    $slots[$index]['saved'] = true;

                    continue;
                }

                if (empty($slot['retryable'])) {
                    $slots[$index]['saved'] = false;

                    continue;
                }

                $directory = $this->hasStoredImage($slots) ? 'images/products/gallery' : 'images/products';
                $downloaded = $this->downloadImage(
                    $url,
                    $directory,
                    (int) ($slot['row'] ?? 0),
                    (string) ($slot['field'] ?? ''),
                    $errors,
                    false
                );

                $slots[$index]['saved'] = $downloaded['path'] !== null;
                $slots[$index]['retryable'] = $downloaded['retryable'];

                if ($downloaded['error'] !== null) {
                    $errors[] = $downloaded['error'];
                }
            }

            $this->syncProductImages($product, $slots);
            array_push($updated, ...$slots);
        }

        return [
            'photos' => $updated,
            'photo_retry' => array_values(array_filter($updated, fn (array $photo): bool => ! empty($photo['retryable']))),
            'errors' => $errors,
        ];
    }

    private function attachImages(Product $product, array $cells, array $headers, int $excelRow, array &$result): void
    {
        $slots = [];

        for ($index = 1; $index <= 21; $index++) {
            $field = 'Foto'.$index;
            $url = $this->text($this->cell($cells, $headers, $field));

            if ($url === null) {
                continue;
            }

            $directory = $this->hasStoredImage($slots) ? 'images/products/gallery' : 'images/products';
            $downloaded = $this->downloadImage($url, $directory, $excelRow, $field, $result['errors']);
            $slot = [
                'product_id' => $product->id,
                'row' => $excelRow,
                'field' => $field,
                'url' => $url,
                'saved' => $downloaded['path'] !== null,
                'retryable' => $downloaded['retryable'],
            ];
            $slots[] = $slot;
            $result['photos'][] = $slot;

            if ($downloaded['error'] !== null) {
                $result['errors'][] = $downloaded['error'];
            }

            if ($downloaded['retryable']) {
                $result['photo_retry'][] = [
                    'product_id' => $product->id,
                    'row' => $excelRow,
                    'field' => $field,
                    'url' => $url,
                ];
            }
        }

        $this->syncProductImages($product, $slots);
    }

    /**
     * @param  array<int, array<string, mixed>>  $slots
     */
    private function hasStoredImage(array $slots): bool
    {
        foreach ($slots as $slot) {
            if ($this->storedImagePath((string) ($slot['url'] ?? '')) !== null) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<int, array<string, mixed>>  $slots
     */
    private function syncProductImages(Product $product, array $slots): void
    {
        usort($slots, fn (array $left, array $right): int => $this->fotoIndex((string) ($left['field'] ?? '')) <=> $this->fotoIndex((string) ($right['field'] ?? '')));

        $paths = [];

        foreach ($slots as $slot) {
            $path = $this->storedImagePath((string) ($slot['url'] ?? ''));

            if ($path !== null) {
                $paths[] = $path;
            }
        }

        $product->image = $paths[0] ?? null;
        $product->gallery = array_slice($paths, 1);
        $product->save();
    }

    private function fotoIndex(string $field): int
    {
        return (int) preg_replace('/\D/', '', $field);
    }

    /**
     * @param  array<int, array<string, mixed>>  $errors
     * @return array{path: ?string, retryable: bool, error: ?array<string, mixed>}
     */
    private function downloadImage(?string $url, string $directory, int $excelRow, string $field, array &$errors, bool $recordError = true): array
    {
        if ($url === null || $url === '') {
            return ['path' => null, 'retryable' => false, 'error' => null];
        }

        if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
            $error = $this->imageError($excelRow, $field, $url, 'URL не начинается с http:// или https://');

            if ($recordError) {
                $errors[] = $error;
            }

            return ['path' => null, 'retryable' => false, 'error' => $recordError ? null : $error];
        }

        $stored = $this->storedImagePath($url);

        if ($stored !== null) {
            Log::info('Excel import: image reused', [
                'row' => $excelRow,
                'field' => $field,
                'url' => $url,
            ]);

            return ['path' => $stored, 'retryable' => false, 'error' => null];
        }

        Log::info('Excel import: image start', [
            'row' => $excelRow,
            'field' => $field,
            'url' => $url,
        ]);

        try {
            $response = Http::connectTimeout(5)->timeout(15)->get($url);

            if (! $response->successful() || $response->body() === '') {
                $reason = $response->successful() ? 'пустой ответ' : 'HTTP '.$response->status();

                throw new RuntimeException('Не удалось скачать изображение: '.$reason);
            }

            $extension = $this->extensionFromUrl($url) ?? $this->extensionFromContentType($response->header('Content-Type'));
            $path = $directory.'/'.sha1($url).($extension ? '.'.$extension : '');

            if (! Storage::disk('public')->exists($path)) {
                Storage::disk('public')->put($path, $response->body());
            }

            Log::info('Excel import: image success', [
                'row' => $excelRow,
                'field' => $field,
                'url' => $url,
            ]);

            return ['path' => $path, 'retryable' => false, 'error' => null];
        } catch (\Throwable $exception) {
            $reason = trim($exception->getMessage());
            $storedReason = str_starts_with($reason, 'Не удалось скачать изображение')
                ? trim(substr($reason, strlen('Не удалось скачать изображение:')), ' :')
                : ($reason !== '' ? $reason : 'неизвестная ошибка');
            $error = $this->imageError($excelRow, $field, $url, $storedReason);
            Log::error('Excel import: image failed', [
                'row' => $excelRow,
                'field' => $field,
                'url' => $url,
                'message' => $error['message'],
            ]);

            if ($recordError) {
                $errors[] = $error;
            }

            return [
                'path' => null,
                'retryable' => $this->isRetryableImageReason($storedReason),
                'error' => $recordError ? null : $error,
            ];
        }
    }

    private function isRetryableImageReason(string $reason): bool
    {
        $reason = mb_strtolower($reason);

        if (preg_match('/http\s+404\b/u', $reason) === 1) {
            return false;
        }

        if (preg_match('/http\s+(408|429|500|502|503|504)\b/u', $reason) === 1) {
            return true;
        }

        if (preg_match('/http\s+[45]\d\d\b/u', $reason) === 1) {
            return false;
        }

        foreach (['resolve', 'timed out', 'timeout', 'connection', 'curl error 6', 'curl error 7', 'curl error 28'] as $needle) {
            if (str_contains($reason, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function storedImagePath(string $url): ?string
    {
        if ($url === '') {
            return null;
        }

        $extension = $this->extensionFromUrl($url);
        $suffix = sha1($url).($extension ? '.'.$extension : '');

        foreach (['images/products', 'images/products/gallery'] as $directory) {
            $path = $directory.'/'.$suffix;

            if (Storage::disk('public')->exists($path)) {
                return $path;
            }
        }

        return null;
    }

    private function imageError(int $excelRow, string $field, string $url, string $reason): array
    {
        return [
            'row' => $excelRow,
            'field' => $field,
            'url' => $url,
            'message' => 'Не удалось скачать изображение: '.$reason,
        ];
    }

    private function extensionFromUrl(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $extension = strtolower(pathinfo(is_string($path) ? $path : '', PATHINFO_EXTENSION));

        return in_array($extension, self::IMAGE_EXTENSIONS, true) ? $extension : null;
    }

    private function extensionFromContentType(?string $contentType): ?string
    {
        return match (strtolower(trim(strtok((string) $contentType, ';')))) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => null,
        };
    }

    private function thc(mixed $value): ?string
    {
        $value = $this->text($value);

        if ($value === null) {
            return null;
        }

        $value = str_replace(['%', ' '], '', $value);

        return $value === '' ? null : $value;
    }

    private function cbd(mixed $value): ?float
    {
        $value = $this->text($value);

        if ($value === null) {
            return null;
        }

        $value = str_replace(['%', ' '], '', $value);

        if ($value === '' || ! is_numeric($value) || (float) $value <= 0) {
            return null;
        }

        return (float) $value;
    }

    private function percent(mixed $value): ?float
    {
        $value = $this->text($value);

        if ($value === null) {
            return null;
        }

        $value = str_replace(['%', ' '], '', $value);

        if ($value === '') {
            return null;
        }

        if (str_contains($value, '-')) {
            $value = substr($value, strrpos($value, '-') + 1);
        }

        return is_numeric($value) ? (float) $value : null;
    }

    private function warnUnmapped(array &$result, int $excelRow, array $cells, array $headers, string $field): void
    {
        if ($this->text($this->cell($cells, $headers, $field)) === null) {
            return;
        }

        $result['warnings'][] = [
            'row' => $excelRow,
            'field' => $field,
            'message' => 'Колонка не записана: в текущей модели нет однозначного поля',
        ];
    }

    private function cell(array $cells, array $headers, string $name): mixed
    {
        if (! array_key_exists($name, $headers)) {
            return null;
        }

        return $cells[$headers[$name]] ?? null;
    }

    private function text(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function isEmptyRow(array $cells): bool
    {
        foreach ($cells as $cell) {
            if ($this->text($cell) !== null) {
                return false;
            }
        }

        return true;
    }
}
