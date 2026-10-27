<?php

namespace Tests\Feature;

use App\Jobs\ImportProductsFromExcelJob;
use App\Jobs\RetryFailedProductImagesJob;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\ExcelProductImportService;
use Illuminate\Contracts\Queue\Job as QueueJobContract;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;
use ZipArchive;

class ExcelProductImportServiceTest extends TestCase
{
    use DatabaseTransactions;

    private const JPEG = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////2wBDAf//////////////////////////////////////////////////////////////////////////////////////wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAf/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIQAxAAAAGf/9k=';

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

        Storage::fake('public');
        Http::fake(function ($request) {
            if (str_contains($request->url(), 'missing')) {
                return Http::response('no', 404);
            }

            return Http::response(base64_decode(substr(self::JPEG, strpos(self::JPEG, ',') + 1)), 200, [
                'Content-Type' => 'image/jpeg',
            ]);
        });
    }

    public function test_excel_row_is_normalized_and_imported(): void
    {
        $result = $this->importSheet([
            $this->headers(),
            $this->sampleRow(),
            array_merge([' '], array_fill(0, count($this->headers()) - 1, null)),
        ]);

        $this->assertSame(1, $result['imported']);
        $this->assertSame(0, $result['updated']);
        $this->assertSame(1, $result['skipped']);
        $this->assertSame([], $result['errors']);

        $product = Product::query()->first();

        $this->assertSame("Acapulco Gold Feminised Barney's Farm", $product->name);
        $this->assertSame('F', $product->seed_type);
        $this->assertSame('26-30', $product->thc);
        $this->assertEquals(1.5, (float) $product->cbd);
        $this->assertEquals(70.0, (float) $product->sativa_percent);
        $this->assertEquals(40.0, (float) $product->indica_percent);
        $this->assertSame(['цитрус', 'кофе'], \App\Models\Product::splitCharacteristicTokens($product->taste));
        $this->assertSame(['эйфоричный'], \App\Models\Product::splitCharacteristicTokens($product->effect));
        $this->assertSame(['хвоя'], \App\Models\Product::splitCharacteristicTokens($product->aroma));
        $this->assertSame('100-150', $product->indoor_height);
        $this->assertSame('500-650', $product->yield);
        $this->assertSame('60-70', $product->flowering);
        $this->assertSame('180-220', $product->height);
        $this->assertSame('700-800', $product->outdoor_yield);
        $this->assertSame('September 3rd–4th week', $product->harvest);
        $this->assertSame('Mexican x Colombian', $product->genetics);
        $this->assertSame('A legendary sativa landrace', $product->description);
        $this->assertSame('Second description block', $product->full_description);
        $this->assertSame('images/products/'.sha1('https://cdn.test/one.jpg').'.jpg', $product->image);
        $this->assertSame([
            'images/products/gallery/'.sha1('https://cdn.test/two.webp').'.webp',
        ], $product->gallery);
        $this->assertTrue(Storage::disk('public')->exists($product->image));
        $this->assertTrue(Storage::disk('public')->exists($product->gallery[0]));
        $this->assertFalse(collect($result['errors'])->contains(fn ($error) => $error['field'] === 'Foto Q'));

        $variants = ProductVariant::query()->orderBy('package_size')->get();
        $this->assertCount(2, $variants);
        $this->assertSame('ART-3', $variants[0]->sku);
        $this->assertSame(3, $variants[0]->package_size);
        $this->assertSame('1500.00', $variants[0]->price);
        $this->assertSame('ART-5', $variants[1]->sku);
        $this->assertSame(5, $variants[1]->package_size);
        $this->assertNull($variants[0]->old_price);
        $this->assertNotEmpty($result['warnings']);
        $this->assertTrue(collect($result['warnings'])->contains(fn ($warning) => $warning['field'] === 'Fas Q'));
        $this->assertTrue(collect($result['warnings'])->contains(fn ($warning) => $warning['field'] === 'Dis1'));
        $this->assertTrue(collect($result['warnings'])->contains(fn ($warning) => $warning['field'] === 'Dis2'));
    }

    public function test_headers_are_matched_by_title_not_column_index(): void
    {
        $headers = $this->headers();
        $row = $this->sampleRow();
        $nameIndex = array_search('Name', $headers, true);
        $typeIndex = array_search('Type', $headers, true);
        [$headers[$nameIndex], $headers[$typeIndex]] = [$headers[$typeIndex], $headers[$nameIndex]];
        [$row[$nameIndex], $row[$typeIndex]] = [$row[$typeIndex], $row[$nameIndex]];

        $this->importSheet([$headers, $row]);

        $this->assertSame("Acapulco Gold Feminised Barney's Farm", Product::query()->first()->name);
    }

    public function test_type_labels_map_to_codes(): void
    {
        $rows = [$this->headers()];

        foreach (['Feminised' => 'F', 'Autoflower' => 'A', 'Regular' => 'R', 'Autoregular' => 'AR'] as $label => $code) {
            $row = $this->sampleRow();
            $row[array_search('Name', $this->headers(), true)] = 'Strain '.$label;
            $row[array_search('Type', $this->headers(), true)] = $label;
            $row[array_search('Art1', $this->headers(), true)] = 'SKU-'.$code;
            $row[array_search('Art2', $this->headers(), true)] = '';
            $row[array_search('Fas2', $this->headers(), true)] = '';
            $row[array_search('Price2', $this->headers(), true)] = '';
            $rows[] = $row;
        }

        $this->importSheet($rows);

        $this->assertEqualsCanonicalizing(
            ['A', 'AR', 'F', 'R'],
            Product::query()->pluck('seed_type')->all()
        );
    }

    public function test_single_thc_percent_is_stripped_and_range_is_kept(): void
    {
        $row = $this->sampleRow();
        $row[array_search('THC %', $this->headers(), true)] = '26%';

        $this->importSheet([$this->headers(), $row]);

        $this->assertSame('26', Product::query()->first()->thc);
    }

    public function test_empty_and_zero_cbd_are_null(): void
    {
        $empty = $this->sampleRow();
        $empty[array_search('Name', $this->headers(), true)] = 'Empty Cbd';
        $empty[array_search('CBD %', $this->headers(), true)] = '';
        $empty[array_search('Art1', $this->headers(), true)] = 'EMPTY-CBD';
        $empty[array_search('Art2', $this->headers(), true)] = 'EMPTY-CBD-5';

        $zero = $this->sampleRow();
        $zero[array_search('Name', $this->headers(), true)] = 'Zero Cbd';
        $zero[array_search('CBD %', $this->headers(), true)] = '0';
        $zero[array_search('Art1', $this->headers(), true)] = 'ZERO-CBD';
        $zero[array_search('Art2', $this->headers(), true)] = 'ZERO-CBD-5';

        $this->importSheet([$this->headers(), $empty, $zero]);

        $this->assertNull(Product::query()->where('genetics', 'Mexican x Colombian')->where('name', 'like', 'Empty Cbd%')->first()->cbd);
        $this->assertNull(Product::query()->where('name', 'like', 'Zero Cbd%')->first()->cbd);
    }

    public function test_sativa_and_indica_ranges_use_upper_bound(): void
    {
        $row = $this->sampleRow();
        $row[array_search('Sativa %', $this->headers(), true)] = '60-70';
        $row[array_search('Indica %', $this->headers(), true)] = '30-40';

        $this->importSheet([$this->headers(), $row]);

        $product = Product::query()->first();
        $this->assertEquals(70.0, (float) $product->sativa_percent);
        $this->assertEquals(40.0, (float) $product->indica_percent);
    }

    public function test_one_variant_is_created_from_the_first_pack_columns(): void
    {
        $row = $this->sampleRow();
        foreach (['Art2', 'Fas2', 'Price2', 'Dis2'] as $header) {
            $row[array_search($header, $this->headers(), true)] = '';
        }

        $this->importSheet([$this->headers(), $row]);

        $this->assertSame(1, ProductVariant::query()->count());
        $this->assertSame(3, ProductVariant::query()->first()->package_size);
    }

    public function test_empty_type_is_imported_with_null_seed_type(): void
    {
        $row = $this->sampleRow();
        $row[array_search('Type', $this->headers(), true)] = '   ';

        $result = $this->importSheet([$this->headers(), $row]);
        $product = Product::query()->first();

        $this->assertSame(1, $result['imported']);
        $this->assertSame([], $result['errors']);
        $this->assertNull($product->seed_type);
        $this->assertSame("Acapulco Gold Barney's Farm", $product->name);
        $this->assertStringNotContainsString('Feminised', $product->name);
        $this->assertSame('Mexican x Colombian', $product->genetics);
        $this->assertSame(2, $product->variants()->count());
    }

    public function test_brand_and_name_are_enough_to_import_a_product(): void
    {
        [$headers, $row] = $this->sparseRow([
            'Brand' => 'Doctor Choice',
            'Name' => '  Only Name  ',
        ]);

        $result = $this->importRows([$headers, $row]);
        $product = Product::query()->first();

        $this->assertSame(1, $result['imported']);
        $this->assertSame([], $result['errors']);
        $this->assertSame('Only Name Doctor Choice', $product->name);
        $this->assertSame('Doctor Choice', $product->brand->name);
        $this->assertNull($product->seed_type);
        $this->assertNull($product->price);
        $this->assertNull($product->thc);
        $this->assertNull($product->cbd);
        $this->assertNull($product->genetics);
        $this->assertNull($product->description);
        $this->assertNull($product->image);
        $this->assertSame(0, $product->variants()->count());
        $this->assertFalse($product->isListedInCatalog());
        $this->assertTrue(collect($result['warnings'])->contains(
            fn ($warning) => $warning['message'] === 'Не указана цена — варианты товара не созданы'
        ));
    }

    public function test_genetics_and_type_are_kept_when_other_cells_are_empty(): void
    {
        [$headers, $row] = $this->sparseRow([
            'Brand' => 'Doctor Choice',
            'Name' => 'Partial Strain',
            'Genetics' => 'Skunk x Haze',
            'Type' => 'Autoflower',
        ]);

        $result = $this->importRows([$headers, $row]);
        $product = Product::query()->first();

        $this->assertSame([], $result['errors']);
        $this->assertSame('Partial Strain Autoflower Doctor Choice', $product->name);
        $this->assertSame('A', $product->seed_type);
        $this->assertSame('Skunk x Haze', $product->genetics);
        $this->assertNull($product->thc);
        $this->assertNull($product->sativa_percent);
        $this->assertNull($product->indica_percent);
        $this->assertNull($product->taste);
        $this->assertNull($product->effect);
        $this->assertNull($product->aroma);
        $this->assertSame(0, $product->variants()->count());
    }

    public function test_descriptions_and_some_photos_are_imported_without_other_fields(): void
    {
        [$headers, $row] = $this->sparseRow([
            'Brand' => 'Doctor Choice',
            'Name' => 'Described Strain',
            'Harvest' => 'October',
            'TXT1' => 'Short description',
            'TXT2' => 'Long description',
            'Foto1' => 'https://cdn.test/one.jpg',
            'Foto3' => 'https://cdn.test/three.jpg',
        ]);

        $result = $this->importRows([$headers, $row]);
        $product = Product::query()->first();

        $this->assertSame([], $result['errors']);
        $this->assertSame('Short description', $product->description);
        $this->assertSame('Long description', $product->full_description);
        $this->assertSame('October', $product->harvest);
        $this->assertNotNull($product->image);
        $this->assertCount(1, $product->gallery ?? []);
        $this->assertTrue(Storage::disk('public')->exists($product->image));
        $this->assertNull($product->thc);
        $this->assertSame(0, $product->variants()->count());
    }

    public function test_missing_numeric_characteristics_stay_null(): void
    {
        [$headers, $row] = $this->sparseRow([
            'Brand' => 'Doctor Choice',
            'Name' => 'No Numbers',
            'THC %' => '   ',
            'CBD %' => '',
            'Sativa %' => null,
            'Indica %' => ' ',
            'Height Indoor (cm)' => '',
            'Indoor Yield (g/m²)' => '',
            'Flowering Time (days)' => '',
            'Height Outdoor (cm)' => '',
            'Outdoor Yield (g/plant)' => '',
        ]);

        $this->importRows([$headers, $row]);
        $product = Product::query()->first();

        $this->assertNull($product->thc);
        $this->assertNull($product->cbd);
        $this->assertNull($product->sativa_percent);
        $this->assertNull($product->indica_percent);
        $this->assertNull($product->indoor_height);
        $this->assertNull($product->yield);
        $this->assertNull($product->flowering);
        $this->assertNull($product->height);
        $this->assertNull($product->outdoor_yield);
        $this->assertNotSame('0', (string) $product->thc);
        $this->assertNotSame(0, $product->cbd);
    }

    public function test_partial_variant_keeps_only_the_complete_pack(): void
    {
        [$headers, $row] = $this->sparseRow([
            'Brand' => 'Doctor Choice',
            'Name' => 'One Pack',
            'Genetics' => 'Kept genetics',
            'Art1' => '   ',
            'Fas1' => 3,
            'Price1' => 1500,
            'Art2' => 'DC-5',
            'Fas2' => 5,
            'Price2' => 900,
        ]);

        $result = $this->importRows([$headers, $row]);
        $product = Product::query()->first();
        $variant = $product->variants()->first();

        $this->assertSame([], $result['errors']);
        $this->assertSame('Kept genetics', $product->genetics);
        $this->assertSame(1, $product->variants()->count());
        $this->assertSame('DC-5', $variant->sku);
        $this->assertSame(5, $variant->package_size);
        $this->assertSame('900.00', $variant->price);
        $this->assertSame('900.00', $product->price);
        $this->assertTrue(collect($result['warnings'])->contains(
            fn ($warning) => str_contains($warning['message'], 'вариант 1 не создан')
        ));
    }

    public function test_price_without_a_pack_is_kept_and_does_not_create_a_variant(): void
    {
        [$headers, $row] = $this->sparseRow([
            'Brand' => 'Doctor Choice',
            'Name' => 'Price Only',
            'Price1' => 1800,
        ]);

        $result = $this->importRows([$headers, $row]);
        $product = Product::query()->first();

        $this->assertSame([], $result['errors']);
        $this->assertSame('1800.00', $product->price);
        $this->assertSame(0, $product->variants()->count());
        $this->assertTrue(collect($result['warnings'])->contains(
            fn ($warning) => str_contains($warning['message'], 'вариант 1 не создан')
        ));
    }

    public function test_missing_name_is_reported_and_does_not_create_a_product(): void
    {
        [$headers, $row] = $this->sparseRow([
            'Brand' => 'Doctor Choice',
            'Name' => '   ',
            'Genetics' => 'Should not be stored',
        ]);

        $result = $this->importRows([$headers, $row]);

        $this->assertSame(0, $result['imported']);
        $this->assertSame(0, Product::query()->count());
        $this->assertSame(2, $result['errors'][0]['row']);
        $this->assertSame('Name', $result['errors'][0]['field']);
        $this->assertSame('Строка 2: не указано название', $result['errors'][0]['message']);
    }

    public function test_valid_rows_are_imported_when_another_row_is_invalid(): void
    {
        [$headers, $valid] = $this->sparseRow([
            'Brand' => 'Doctor Choice',
            'Name' => 'Good Strain',
            'Type' => 'Regular',
        ]);
        $missingName = $valid;
        $missingName[array_search('Name', $headers, true)] = '';
        $missingName[array_search('Genetics', $headers, true)] = 'Lost';
        $second = $valid;
        $second[array_search('Name', $headers, true)] = 'Second Strain';

        $result = $this->importRows([$headers, $valid, $missingName, $second]);

        $this->assertSame(2, $result['imported']);
        $this->assertSame(2, Product::query()->count());
        $this->assertSame(1, count($result['errors']));
        $this->assertSame(3, $result['errors'][0]['row']);
        $this->assertSame('Name', $result['errors'][0]['field']);
        $this->assertNull(Product::query()->where('genetics', 'Lost')->first());
    }

    public function test_missing_characteristics_are_not_shown_as_placeholders(): void
    {
        [$headers, $row] = $this->sparseRow([
            'Brand' => 'Doctor Choice',
            'Name' => 'Visible Strain',
            'Art1' => 'DC-3',
            'Fas1' => 3,
            'Price1' => 1100,
        ]);

        $this->importRows([$headers, $row]);
        $product = Product::query()->first();

        $card = view('components.product-card', ['product' => $product])->render();
        $page = $this->get(route('product.show', $product->slug));

        $page->assertOk();
        $page->assertSee('Visible Strain');
        $page->assertDontSee('product-detail-features', false);
        $page->assertDontSee('Не указано');
        $page->assertDontSee('Тип семян');
        $this->assertStringNotContainsString('Тип семян', $card);
        $this->assertStringNotContainsString('Не указано', $card);
    }

    public function test_sparse_product_can_be_imported_again_without_a_duplicate(): void
    {
        [$headers, $row] = $this->sparseRow([
            'Brand' => 'Doctor Choice',
            'Name' => 'Repeat Strain',
            'Genetics' => 'First genetics',
        ]);
        $file = $this->workbook([$headers, $row]);

        $first = app(ExcelProductImportService::class)->import($file, '');
        $row[array_search('Genetics', $headers, true)] = 'Updated genetics';
        $secondFile = $this->workbook([$headers, $row]);
        $second = app(ExcelProductImportService::class)->import($secondFile, '');

        $this->assertSame(1, $first['imported']);
        $this->assertSame(1, $second['updated']);
        $this->assertSame(1, Product::query()->count());
        $this->assertSame('Updated genetics', Product::query()->first()->genetics);
    }

    public function test_descriptive_seed_types_use_the_existing_codes(): void
    {
        $rows = [$this->headers()];
        $cases = [
            'Autoflower Feminised' => 'A',
            'Feminised (Photoperiod)' => 'F',
            'Regular (Photoperiod)' => 'R',
            'Autoflower Regular' => 'AR',
            'Fast Feminised (mutantfarmer)' => 'F',
        ];

        foreach ($cases as $label => $code) {
            $row = $this->sampleRow();
            $row[array_search('Name', $this->headers(), true)] = 'Strain '.$label;
            $row[array_search('Type', $this->headers(), true)] = $label;
            $row[array_search('Art1', $this->headers(), true)] = 'SKU-'.substr(md5($label), 0, 8);
            $row[array_search('Art2', $this->headers(), true)] = '';
            $row[array_search('Fas2', $this->headers(), true)] = '';
            $row[array_search('Price2', $this->headers(), true)] = '';
            $rows[] = $row;
        }

        $result = $this->importSheet($rows);

        $this->assertSame([], $result['errors']);
        $this->assertSame(5, Product::query()->count());
        $this->assertEqualsCanonicalizing(
            ['A', 'AR', 'F', 'F', 'R'],
            Product::query()->pluck('seed_type')->all()
        );
        $this->assertTrue(collect($result['warnings'])->contains(
            fn ($warning) => str_contains($warning['message'], 'Type «Autoflower Feminised» записан как Autoflower')
        ));
    }

    public function test_price_and_pack_count_without_sku_do_not_invent_a_variant(): void
    {
        [$headers, $row] = $this->sparseRow([
            'Brand' => "Barney's Farm",
            'Name' => 'Devotchka',
            'Type' => 'Autoflower Feminised',
            'Fas Q' => 5,
            'Price1' => 84,
            'TXT1' => 'Short description',
        ]);

        $result = $this->importRows([$headers, $row]);
        $product = Product::query()->first();

        $this->assertSame([], $result['errors']);
        $this->assertSame('Devotchka Autoflower Barney\'s Farm', $product->name);
        $this->assertSame('A', $product->seed_type);
        $this->assertSame('84.00', $product->price);
        $this->assertSame('Short description', $product->description);
        $this->assertSame(0, $product->variants()->count());
        $this->assertTrue(collect($result['warnings'])->contains(
            fn ($warning) => $warning['field'] === 'Fas Q'
        ));
        $this->assertTrue(collect($result['warnings'])->contains(
            fn ($warning) => str_contains($warning['message'], 'вариант 1 не создан')
        ));

        $this->get(route('product.show', $product->slug))
            ->assertOk()
            ->assertSee('Devotchka')
            ->assertDontSee('product-detail-features', false);
    }

    public function test_unknown_type_is_reported(): void
    {
        $row = $this->sampleRow();
        $row[array_search('Type', $this->headers(), true)] = 'Fast';

        $result = $this->importSheet([$this->headers(), $row]);

        $this->assertSame(0, Product::query()->count());
        $this->assertSame('Type', $result['errors'][0]['field']);
        $this->assertSame('Неизвестный тип семян', $result['errors'][0]['message']);
    }

    public function test_failed_photo_is_reported_and_the_product_stays(): void
    {
        $row = $this->sampleRow();
        $row[array_search('Foto2', $this->headers(), true)] = 'https://cdn.test/missing-foto7.jpg';
        $headers = $this->headers();
        $foto2 = array_search('Foto2', $headers, true);
        $headers[$foto2] = 'Foto7';

        $result = $this->importSheet([$headers, $row]);

        $this->assertSame(1, $result['imported']);
        $this->assertSame(1, Product::query()->count());
        $error = collect($result['errors'])->firstWhere('field', 'Foto7');
        $this->assertNotNull($error);
        $this->assertSame('Не удалось скачать изображение: HTTP 404', $error['message']);
        $this->assertSame('https://cdn.test/missing-foto7.jpg', $error['url']);
        $this->assertNotNull(Product::query()->first()->image);
    }

    public function test_reimport_does_not_duplicate_the_product_or_photos(): void
    {
        $sheet = [$this->headers(), $this->sampleRow()];
        $file = $this->workbook($sheet);

        $first = app(ExcelProductImportService::class)->import($file, "Barney's Farm");
        $filesAfterFirst = count(Storage::disk('public')->allFiles());
        $image = Product::query()->first()->image;
        $gallery = Product::query()->first()->gallery;

        $second = app(ExcelProductImportService::class)->import($file, "Barney's Farm");

        $this->assertSame(1, $first['imported']);
        $this->assertSame(1, $second['updated']);
        $this->assertSame(1, Product::query()->count());
        $this->assertSame(2, ProductVariant::query()->count());
        $this->assertSame($filesAfterFirst, count(Storage::disk('public')->allFiles()));
        $this->assertSame($image, Product::query()->first()->image);
        $this->assertSame($gallery, Product::query()->first()->gallery);
        Http::assertSentCount(2);
    }

    public function test_brand_column_is_used_when_txt2_contains_another_spelling(): void
    {
        $headers = array_merge(['Brand'], $this->headers());
        $row = array_merge(["Barney's Farm"], $this->sampleRow());
        $txt2 = "Apple Fritter Strain By Barney\u{2018}s Farm";
        $row[array_search('TXT2', $headers, true)] = $txt2;

        $result = app(ExcelProductImportService::class)->import($this->workbook([$headers, $row]), '');

        $product = Product::query()->first();
        $this->assertSame([], $result['errors']);
        $this->assertSame("Barney's Farm", $product->brand->name);
        $this->assertSame($txt2, $product->full_description);
        $this->assertSame(1, Brand::query()->count());
    }

    public function test_txt2_is_not_used_to_detect_the_brand(): void
    {
        $headers = array_merge(['Brand'], $this->headers());
        $row = array_merge(["Barney's Farm"], $this->sampleRow());
        $row[array_search('TXT2', $headers, true)] = 'Something completely different';

        app(ExcelProductImportService::class)->import($this->workbook([$headers, $row]), '');

        $product = Product::query()->first();
        $this->assertSame("Barney's Farm", $product->brand->name);
        $this->assertSame('Something completely different', $product->full_description);
    }

    public function test_empty_brand_column_uses_the_filament_fallback(): void
    {
        $headers = array_merge(['Brand'], $this->headers());
        $row = array_merge([''], $this->sampleRow());
        $row[array_search('TXT2', $headers, true)] = "Apple Fritter Strain By Barney\u{2018}s Farm";

        app(ExcelProductImportService::class)->import($this->workbook([$headers, $row]), "Barney's Farm");

        $this->assertSame("Barney's Farm", Product::query()->first()->brand->name);
        $this->assertSame(1, Brand::query()->count());
    }

    public function test_row_without_a_brand_is_not_imported(): void
    {
        $headers = array_merge(['Brand'], $this->headers());
        $row = array_merge([''], $this->sampleRow());
        $row[array_search('TXT2', $headers, true)] = "Apple Fritter Strain By Barney's Farm";

        $result = app(ExcelProductImportService::class)->import($this->workbook([$headers, $row]), '');

        $this->assertSame(0, Product::query()->count());
        $this->assertSame(0, Brand::query()->count());
        $this->assertSame(2, $result['errors'][0]['row']);
        $this->assertSame('Brand', $result['errors'][0]['field']);
        $this->assertSame('Строка 2: не удалось определить бренд — колонка Brand пустая', $result['errors'][0]['message']);
    }

    public function test_brand_column_is_used_when_txt2_has_no_brand(): void
    {
        $headers = array_merge(['Brand'], $this->headers());
        $row = array_merge(['Dutch Bulk'], $this->sampleRow());
        $row[array_search('TXT2', $headers, true)] = 'Acapulco Gold — описание сорта...';

        $result = app(ExcelProductImportService::class)->import($this->workbook([$headers, $row]), '');

        $this->assertSame([], $result['errors']);
        $this->assertSame('Dutch Bulk', Product::query()->first()->brand->name);
        $this->assertSame('Acapulco Gold — описание сорта...', Product::query()->first()->full_description);
    }

    public function test_foto_q_count_is_not_downloaded(): void
    {
        $result = $this->importPhotos([
            'Foto1' => 'https://cdn.test/one.jpg',
            'Foto2' => 'https://cdn.test/two.webp',
        ], 10);

        $product = Product::query()->first();

        $this->assertSame([], $result['errors']);
        $this->assertFalse(collect($result['errors'])->contains(fn ($error) => $error['url'] === '10' || $error['field'] === 'Foto Q'));
        $this->assertSame('images/products/'.sha1('https://cdn.test/one.jpg').'.jpg', $product->image);
        $this->assertSame([
            'images/products/gallery/'.sha1('https://cdn.test/two.webp').'.webp',
        ], $product->gallery);
        Http::assertSentCount(2);
    }

    public function test_first_successful_photo_is_the_main_image(): void
    {
        $this->importPhotos([
            'Foto1' => 'https://cdn.test/one.jpg',
            'Foto2' => 'https://cdn.test/two.webp',
            'Foto3' => 'https://cdn.test/three.png',
        ]);

        $product = Product::query()->first();

        $this->assertSame('images/products/'.sha1('https://cdn.test/one.jpg').'.jpg', $product->image);
        $this->assertSame([
            'images/products/gallery/'.sha1('https://cdn.test/two.webp').'.webp',
            'images/products/gallery/'.sha1('https://cdn.test/three.png').'.png',
        ], $product->gallery);
    }

    public function test_failed_first_photo_is_skipped_for_the_main_image(): void
    {
        $result = $this->importPhotos([
            'Foto1' => 'https://cdn.test/missing-one.jpg',
            'Foto2' => 'https://cdn.test/two.webp',
            'Foto3' => 'https://cdn.test/three.png',
        ]);

        $product = Product::query()->first();

        $this->assertSame('images/products/'.sha1('https://cdn.test/two.webp').'.webp', $product->image);
        $this->assertSame([
            'images/products/gallery/'.sha1('https://cdn.test/three.png').'.png',
        ], $product->gallery);
        $error = collect($result['errors'])->firstWhere('field', 'Foto1');
        $this->assertSame(2, $error['row']);
        $this->assertSame('https://cdn.test/missing-one.jpg', $error['url']);
        $this->assertSame('Не удалось скачать изображение: HTTP 404', $error['message']);
    }

    public function test_failed_middle_photo_is_reported_and_skipped_in_the_gallery(): void
    {
        $result = $this->importPhotos([
            'Foto1' => 'https://cdn.test/one.jpg',
            'Foto2' => 'https://cdn.test/missing-two.jpg',
            'Foto3' => 'https://cdn.test/three.png',
        ]);

        $product = Product::query()->first();

        $this->assertSame(1, $result['imported']);
        $this->assertSame('images/products/'.sha1('https://cdn.test/one.jpg').'.jpg', $product->image);
        $this->assertSame([
            'images/products/gallery/'.sha1('https://cdn.test/three.png').'.png',
        ], $product->gallery);
        $error = collect($result['errors'])->firstWhere('field', 'Foto2');
        $this->assertSame(2, $error['row']);
        $this->assertSame('https://cdn.test/missing-two.jpg', $error['url']);
        $this->assertSame('Не удалось скачать изображение: HTTP 404', $error['message']);
    }

    public function test_product_without_prices_is_imported_without_variants(): void
    {
        $headers = $this->headers();
        $row = $this->sampleRow();

        foreach (['Price1', 'Price2', 'Fas1', 'Fas2', 'Art1', 'Art2'] as $field) {
            $row[array_search($field, $headers, true)] = '';
        }

        $result = $this->importSheet([$headers, $row]);
        $product = Product::query()->first();

        $warning = collect($result['warnings'])->firstWhere('field', 'Price1');

        $this->assertSame(1, $result['imported']);
        $this->assertSame([], $result['errors']);
        $this->assertSame('Не указана цена — варианты товара не созданы', $warning['message']);
        $this->assertSame(2, $warning['row']);
        $this->assertNull($product->price);
        $this->assertTrue($product->is_visible);
        $this->assertFalse($product->isListedInCatalog());
        $this->assertNotNull(Product::query()->find($product->id));
        $this->assertSame(0, $product->variants()->count());
        $this->assertSame('A legendary sativa landrace', $product->description);
        $this->assertSame("Barney's Farm", $product->brand->name);
    }

    public function test_excel_row_number_is_stored_on_create_and_update(): void
    {
        $blank = array_fill(0, count($this->headers()), null);
        $rows = [$this->headers()];

        for ($i = 0; $i < 6; $i++) {
            $rows[] = $blank;
        }

        $rows[] = $this->sampleRow();

        $created = $this->importSheet($rows);
        $product = Product::query()->first();

        $this->assertSame(1, $created['imported']);
        $this->assertSame(8, $product->excel_row);

        $updated = $this->importSheet($rows);

        $this->assertSame(1, $updated['updated']);
        $this->assertSame(8, $product->fresh()->excel_row);
    }

    public function test_product_without_price_stores_its_excel_row(): void
    {
        $headers = $this->headers();
        $row = $this->sampleRow();
        $blank = array_fill(0, count($headers), null);

        foreach (['Price1', 'Price2', 'Fas1', 'Fas2', 'Art1', 'Art2'] as $field) {
            $row[array_search($field, $headers, true)] = '';
        }

        $this->importSheet([$headers, $blank, $blank, $row]);
        $product = Product::query()->first();

        $this->assertSame(4, $product->excel_row);
        $this->assertNull($product->price);
        $this->assertSame(0, $product->variants()->count());
    }

    public function test_photos_are_imported_when_the_price_is_missing(): void
    {
        $headers = $this->headers();
        $row = $this->sampleRow();

        foreach (['Price1', 'Price2', 'Fas1', 'Fas2', 'Art1', 'Art2'] as $field) {
            $row[array_search($field, $headers, true)] = '';
        }

        $row[array_search('Foto1', $headers, true)] = 'https://cdn.test/one.jpg';

        $this->importSheet([$headers, $row]);

        $this->assertSame(
            'images/products/'.sha1('https://cdn.test/one.jpg').'.jpg',
            Product::query()->first()->image
        );
    }

    public function test_image_error_keeps_row_field_url_and_message(): void
    {
        $result = $this->importPhotos([
            'Foto1' => 'https://cdn.test/missing-one.jpg',
        ]);

        $error = $result['errors'][0];

        $this->assertSame(2, $error['row']);
        $this->assertSame('Foto1', $error['field']);
        $this->assertSame('https://cdn.test/missing-one.jpg', $error['url']);
        $this->assertSame('Не удалось скачать изображение: HTTP 404', $error['message']);
        $this->assertSame([], $result['photo_retry']);
    }

    public function test_dns_failure_is_queued_for_a_photo_retry(): void
    {
        Http::fake(function () {
            throw new ConnectionException('cURL error 6: Could not resolve host: www.barneysfarm.com');
        });

        $headers = $this->headers();
        $row = $this->sampleRow();
        $row[array_search('Foto1', $headers, true)] = 'https://www.barneysfarm.com/images/products/one.jpg';
        $row[array_search('Foto2', $headers, true)] = '';

        $result = $this->importSheet([$headers, $row]);

        $this->assertSame(1, $result['imported']);
        $this->assertSame(Product::query()->first()->id, $result['photo_retry'][0]['product_id']);
        $this->assertSame(2, $result['photo_retry'][0]['row']);
        $this->assertSame('Foto1', $result['photo_retry'][0]['field']);
        $this->assertSame('https://www.barneysfarm.com/images/products/one.jpg', $result['photo_retry'][0]['url']);
    }

    public function test_import_job_stores_the_full_report_and_delays_the_photo_retry(): void
    {
        Queue::fake();
        Http::fake(function () {
            throw new ConnectionException('cURL error 28: Resolving timed out after 5000 milliseconds');
        });
        Storage::fake('local');

        $headers = $this->headers();
        $row = $this->sampleRow();
        $row[array_search('Foto1', $headers, true)] = 'https://www.barneysfarm.com/images/products/one.jpg';
        $row[array_search('Foto2', $headers, true)] = '';
        $contents = file_get_contents($this->workbook([$headers, $row]));
        Storage::disk('local')->put('imports/catalog.xlsx', $contents);

        (new ImportProductsFromExcelJob('imports/catalog.xlsx', "Barney's Farm"))
            ->handle(app(ExcelProductImportService::class));

        Storage::disk('local')->assertMissing('imports/catalog.xlsx');
        $reports = Storage::disk('local')->allFiles('imports/reports');
        $this->assertCount(1, $reports);
        $report = json_decode(Storage::disk('local')->get($reports[0]), true);
        $this->assertSame(1, $report['imported']);
        $this->assertSame('Foto1', $report['errors'][0]['field']);
        $this->assertSame('https://www.barneysfarm.com/images/products/one.jpg', $report['errors'][0]['url']);
        $this->assertCount(1, $report['photo_retry']);

        Queue::assertPushed(RetryFailedProductImagesJob::class, function (RetryFailedProductImagesJob $job): bool {
            return $job->delay !== null && $job->delay->greaterThan(now()->addMinutes(4));
        });
    }

    public function test_retry_downloads_a_failed_photo_and_keeps_foto_order(): void
    {
        $failTwo = true;
        Http::fake(function ($request) use (&$failTwo) {
            if ($failTwo && str_contains($request->url(), 'two')) {
                throw new ConnectionException('cURL error 6: Could not resolve host');
            }

            return Http::response(base64_decode(substr(self::JPEG, strpos(self::JPEG, ',') + 1)), 200, [
                'Content-Type' => 'image/jpeg',
            ]);
        });

        $result = $this->importPhotos([
            'Foto1' => 'https://cdn.test/one.jpg',
            'Foto2' => 'https://cdn.test/two.jpg',
            'Foto3' => 'https://cdn.test/three.jpg',
        ]);
        $product = Product::query()->first();

        $this->assertSame('images/products/'.sha1('https://cdn.test/one.jpg').'.jpg', $product->image);
        $this->assertSame([
            'images/products/gallery/'.sha1('https://cdn.test/three.jpg').'.jpg',
        ], $product->gallery);

        $failTwo = false;

        app(ExcelProductImportService::class)->retryProductImages($result['photos']);
        $product->refresh();

        $this->assertSame('images/products/'.sha1('https://cdn.test/one.jpg').'.jpg', $product->image);
        $this->assertSame([
            'images/products/gallery/'.sha1('https://cdn.test/two.jpg').'.jpg',
            'images/products/gallery/'.sha1('https://cdn.test/three.jpg').'.jpg',
        ], $product->gallery);
    }

    public function test_retry_does_not_download_an_existing_file(): void
    {
        $url = 'https://cdn.test/one.jpg';
        $path = 'images/products/'.sha1($url).'.jpg';
        $this->importSheet([$this->headers(), $this->sampleRow()]);
        $stored = Product::query()->first();

        Http::fake(function () {
            throw new ConnectionException('cURL error 6: Could not resolve host');
        });

        $outcome = app(ExcelProductImportService::class)->retryProductImages([[
            'product_id' => $stored->id,
            'row' => 2,
            'field' => 'Foto1',
            'url' => $url,
            'retryable' => true,
            'saved' => false,
        ]]);

        Http::assertNothingSent();
        $this->assertSame([], $outcome['photo_retry']);
        $this->assertSame($path, $stored->fresh()->image);
    }

    public function test_http_404_is_not_scheduled_for_retry(): void
    {
        $result = $this->importPhotos([
            'Foto1' => 'https://cdn.test/missing-one.jpg',
            'Foto2' => 'https://cdn.test/two.webp',
        ]);

        $this->assertSame([], $result['photo_retry']);
        $this->assertSame(
            'images/products/'.sha1('https://cdn.test/two.webp').'.webp',
            Product::query()->first()->image
        );

        Http::fake(function () {
            throw new ConnectionException('cURL error 6: Could not resolve host');
        });

        $outcome = app(ExcelProductImportService::class)->retryProductImages($result['photos']);

        Http::assertNothingSent();
        $this->assertSame([], $outcome['photo_retry']);
    }

    public function test_inflated_worksheet_dimension_does_not_count_empty_rows_as_skipped(): void
    {
        $file = $this->workbook([$this->headers(), $this->sampleRow()]);
        $zip = new ZipArchive();
        $zip->open($file);
        $xml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $xml = preg_replace('/<dimension ref="[^"]+"/', '<dimension ref="A1:AZ1048576"', $xml, 1);
        $zip->addFromString('xl/worksheets/sheet1.xml', $xml);
        $zip->close();

        $result = app(ExcelProductImportService::class)->import($file, "Barney's Farm");

        $this->assertSame(1, $result['imported']);
        $this->assertSame(0, $result['skipped']);
    }

    public function test_retry_job_restores_gallery_order_for_a_product_without_price(): void
    {
        $failTwo = true;
        Http::fake(function ($request) use (&$failTwo) {
            if ($failTwo && str_contains($request->url(), 'two')) {
                throw new ConnectionException('cURL error 28: Resolving timed out after 5000 milliseconds');
            }

            return Http::response(base64_decode(substr(self::JPEG, strpos(self::JPEG, ',') + 1)), 200, [
                'Content-Type' => 'image/jpeg',
            ]);
        });

        $headers = $this->headers();
        $row = $this->sampleRow();

        foreach (['Price1', 'Price2', 'Fas1', 'Fas2', 'Art1', 'Art2'] as $field) {
            $row[array_search($field, $headers, true)] = '';
        }

        $row[array_search('Foto1', $headers, true)] = 'https://cdn.test/one.jpg';
        $row[array_search('Foto2', $headers, true)] = 'https://cdn.test/two.jpg';
        $headers[] = 'Foto3';
        $row[] = 'https://cdn.test/three.jpg';

        $result = $this->importSheet([$headers, $row]);
        $product = Product::query()->first();
        $two = 'images/products/gallery/'.sha1('https://cdn.test/two.jpg').'.jpg';

        $this->assertNull($product->price);
        $this->assertSame(0, $product->variants()->count());
        $this->assertSame('images/products/'.sha1('https://cdn.test/one.jpg').'.jpg', $product->image);
        $this->assertSame([
            'images/products/gallery/'.sha1('https://cdn.test/three.jpg').'.jpg',
        ], $product->gallery);
        $this->assertSame('Foto2', $result['photo_retry'][0]['field']);
        $this->assertFalse(Storage::disk('public')->exists($two));

        Storage::fake('local');
        $path = 'imports/reports/retry-order.json';
        Storage::disk('local')->put($path, json_encode([
            'file' => 'imports/sample.xlsx',
            'imported' => $result['imported'],
            'updated' => $result['updated'],
            'skipped' => $result['skipped'],
            'errors' => $result['errors'],
            'warnings' => $result['warnings'],
            'photo_retry' => $result['photo_retry'],
            'photos' => $result['photos'],
            'started_at' => now()->toIso8601String(),
            'finished_at' => now()->toIso8601String(),
        ], JSON_UNESCAPED_UNICODE));

        $failTwo = false;
        (new RetryFailedProductImagesJob($path))->handle(app(ExcelProductImportService::class));
        $product->refresh();

        $this->assertNull($product->price);
        $this->assertSame(0, $product->variants()->count());
        $this->assertSame('images/products/'.sha1('https://cdn.test/one.jpg').'.jpg', $product->image);
        $this->assertSame([
            $two,
            'images/products/gallery/'.sha1('https://cdn.test/three.jpg').'.jpg',
        ], $product->gallery);
        $this->assertTrue(Storage::disk('public')->exists($two));

        $report = json_decode(Storage::disk('local')->get($path), true);
        $this->assertSame([], $report['photo_retry']);
        $this->assertFalse(collect($report['errors'])->contains(fn ($error) => ($error['field'] ?? null) === 'Foto2'));
        $this->assertTrue(collect($report['photos'])->contains(
            fn ($photo) => $photo['field'] === 'Foto2' && $photo['saved'] === true && $photo['retryable'] === false
        ));

        Http::fake(function () {
            throw new ConnectionException('cURL error 6: Could not resolve host');
        });

        (new RetryFailedProductImagesJob($path))->handle(app(ExcelProductImportService::class));

        Http::assertNothingSent();
        $this->assertSame($product->image, $product->fresh()->image);
        $this->assertSame($product->gallery, $product->fresh()->gallery);
    }

    public function test_retry_job_keeps_the_error_after_the_last_attempt(): void
    {
        Http::fake(function () {
            throw new ConnectionException('cURL error 6: Could not resolve host');
        });

        $brand = Brand::query()->create(['name' => 'Retry Attempts Farm']);
        $product = Product::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Retry Attempts Strain',
            'price' => null,
            'seed_type' => 'F',
        ]);

        Storage::fake('local');
        $path = 'imports/reports/retry-attempts.json';
        Storage::disk('local')->put($path, json_encode([
            'file' => 'imports/sample.xlsx',
            'imported' => 1,
            'updated' => 0,
            'skipped' => 0,
            'errors' => [],
            'warnings' => [],
            'photo_retry' => [[
                'product_id' => $product->id,
                'row' => 6,
                'field' => 'Foto1',
                'url' => 'https://www.barneysfarm.com/images/products/apple.jpg',
            ]],
            'photos' => [[
                'product_id' => $product->id,
                'row' => 6,
                'field' => 'Foto1',
                'url' => 'https://www.barneysfarm.com/images/products/apple.jpg',
                'saved' => false,
                'retryable' => true,
            ]],
            'started_at' => now()->toIso8601String(),
            'finished_at' => now()->toIso8601String(),
        ], JSON_UNESCAPED_UNICODE));

        $job = new RetryFailedProductImagesJob($path);

        foreach ([1, 2] as $attempt) {
            $queueJob = \Mockery::mock(QueueJobContract::class);
            $queueJob->shouldReceive('attempts')->andReturn($attempt);
            $job->setJob($queueJob);

            try {
                $job->handle(app(ExcelProductImportService::class));
                $this->fail('Попытка '.$attempt.' должна остаться в очереди.');
            } catch (RuntimeException $exception) {
                $this->assertSame('Остались фотографии для повторной загрузки.', $exception->getMessage());
            }
        }

        $queueJob = \Mockery::mock(QueueJobContract::class);
        $queueJob->shouldReceive('attempts')->andReturn(3);
        $job->setJob($queueJob);
        $job->handle(app(ExcelProductImportService::class));

        $report = json_decode(Storage::disk('local')->get($path), true);

        $this->assertSame($product->id, $report['photo_retry'][0]['product_id']);
        $this->assertSame(6, $report['photo_retry'][0]['row']);
        $this->assertSame('Foto1', $report['photo_retry'][0]['field']);
        $this->assertSame('https://www.barneysfarm.com/images/products/apple.jpg', $report['photo_retry'][0]['url']);
        $this->assertSame('Foto1', $report['errors'][0]['field']);
        $this->assertStringContainsString('Could not resolve host', $report['errors'][0]['message']);
        $this->assertNull($product->fresh()->image);
        $this->assertSame([], $product->fresh()->gallery ?? []);
    }

    public function test_image_retry_follows_the_retryable_error_rules(): void
    {
        $brand = Brand::query()->create(['name' => 'Retry Rules Farm']);
        $product = Product::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Retry Rules Strain',
            'price' => null,
            'seed_type' => 'F',
        ]);
        $reason = '';

        Http::fake(function () use (&$reason) {
            if (str_starts_with($reason, 'Не удалось')) {
                throw new RuntimeException($reason);
            }

            throw new ConnectionException($reason);
        });

        $cases = [
            'cURL error 6: Could not resolve host' => true,
            'cURL error 7: Failed to connect' => true,
            'cURL error 28: Connection timed out' => true,
            'Resolving timed out after 5000 ms' => true,
            'connection failed' => true,
            'Не удалось скачать изображение: HTTP 408' => true,
            'Не удалось скачать изображение: HTTP 429' => true,
            'Не удалось скачать изображение: HTTP 500' => true,
            'Не удалось скачать изображение: HTTP 502' => true,
            'Не удалось скачать изображение: HTTP 503' => true,
            'Не удалось скачать изображение: HTTP 504' => true,
            'Не удалось скачать изображение: HTTP 404' => false,
            'Не удалось скачать изображение: HTTP 403' => false,
            'Не удалось скачать изображение: HTTP 401' => false,
        ];

        foreach ($cases as $message => $retryable) {
            $reason = $message;
            $outcome = app(ExcelProductImportService::class)->retryProductImages([[
                'product_id' => $product->id,
                'row' => 4,
                'field' => 'Foto1',
                'url' => 'https://cdn.test/rule-'.sha1($message).'.jpg',
                'saved' => false,
                'retryable' => true,
            ]]);

            $this->assertSame($retryable, $outcome['photo_retry'] !== [], $message);
        }

        $empty = app(ExcelProductImportService::class)->retryProductImages([[
            'product_id' => $product->id,
            'row' => 4,
            'field' => 'Foto2',
            'url' => '',
            'saved' => false,
            'retryable' => true,
        ]]);
        $invalid = app(ExcelProductImportService::class)->retryProductImages([[
            'product_id' => $product->id,
            'row' => 4,
            'field' => 'Foto3',
            'url' => 'notaurl',
            'saved' => false,
            'retryable' => true,
        ]]);

        $this->assertSame([], $empty['photo_retry']);
        $this->assertSame([], $invalid['photo_retry']);
        $this->assertSame('Foto3', $invalid['errors'][0]['field']);
        $this->assertNull($product->fresh()->image);
    }

    private function importPhotos(array $photos, mixed $fotoQ = 10): array
    {
        $headers = $this->headers();
        $row = $this->sampleRow();
        $row[array_search('Foto Q', $headers, true)] = $fotoQ;

        foreach (['Foto1', 'Foto2'] as $field) {
            if (array_key_exists($field, $photos)) {
                $row[array_search($field, $headers, true)] = $photos[$field];
            }
        }

        foreach ($photos as $field => $url) {
            if (in_array($field, ['Foto1', 'Foto2'], true)) {
                continue;
            }

            $headers[] = $field;
            $row[] = $url;
        }

        return $this->importSheet([$headers, $row]);
    }

    public function test_missing_catalog_column_keeps_visibility_on_update(): void
    {
        $this->importSheet([$this->headers(), $this->sampleRow()]);
        $product = Product::query()->first();
        $product->is_visible = false;
        $product->save();

        $this->importSheet([$this->headers(), $this->sampleRow()]);

        $this->assertFalse($product->fresh()->is_visible);
    }

    public function test_catalog_column_sets_visibility_without_using_price(): void
    {
        $headers = array_merge($this->headers(), ['Показывать в каталоге']);
        $hidden = array_merge($this->sampleRow(), ['нет']);
        $shown = array_merge($this->sampleRow(), ['да']);

        foreach (['Price1', 'Price2', 'Fas1', 'Fas2', 'Art1', 'Art2'] as $field) {
            $hidden[array_search($field, $headers, true)] = '';
            $shown[array_search($field, $headers, true)] = '';
        }

        $this->importSheet([$headers, $hidden]);
        $product = Product::query()->first();

        $this->assertFalse($product->is_visible);
        $this->assertNull($product->price);

        $this->importSheet([$headers, $shown]);

        $this->assertTrue($product->fresh()->is_visible);
        $this->assertNull($product->fresh()->price);
    }

    public function test_semicolon_csv_uses_the_same_importer(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'csv');
        $target = $path.'.csv';
        rename($path, $target);
        file_put_contents($target, "Name;Type;Brand;Art1;Fas1;Price1\nCsv Strain;Feminised;Csv Farm;SKU-CSV;3;100\n");

        $result = app(ExcelProductImportService::class)->import($target, '');
        $product = Product::query()->where('name', 'like', 'Csv Strain%')->first();

        $this->assertSame(1, $result['imported']);
        $this->assertNotNull($product);
        $this->assertTrue($product->is_visible);
        $this->assertSame('Csv Farm', $product->brand->name);
    }

    private function importSheet(array $rows): array
    {
        return app(ExcelProductImportService::class)->import($this->workbook($rows), "Barney's Farm");
    }

    private function importRows(array $rows): array
    {
        return app(ExcelProductImportService::class)->import($this->workbook($rows), '');
    }

    private function sparseRow(array $values): array
    {
        $headers = array_merge(['Brand'], $this->headers());
        $fotoIndex = array_search('Foto2', $headers, true);
        array_splice($headers, $fotoIndex + 1, 0, array_map(
            fn (int $index) => 'Foto'.$index,
            range(3, 21)
        ));
        $row = array_fill(0, count($headers), null);

        foreach ($values as $header => $value) {
            $index = array_search($header, $headers, true);
            $this->assertNotFalse($index, $header);
            $row[$index] = $value;
        }

        return [$headers, $row];
    }

    private function workbook(array $rows): string
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->fromArray($rows, null, 'A1');
        $path = tempnam(sys_get_temp_dir(), 'xlsx');
        $target = $path.'.xlsx';
        rename($path, $target);
        (new Xlsx($spreadsheet))->save($target);

        return $target;
    }

    private function headers(): array
    {
        return [
            'Name', 'Genetics', 'THC %', 'CBD %', 'Type', 'Sativa %', 'Indica %',
            'Вкус', 'Эффект', 'Аромат',
            'Height Indoor (cm)', 'Indoor Yield (g/m²)', 'Flowering Time (days)',
            'Height Outdoor (cm)', 'Outdoor Yield (g/plant)', 'Harvest',
            'TXT1', 'TXT2', 'Foto Q', 'Foto1', 'Foto2',
            'Fas Q', 'Art1', 'Fas1', 'Price1', 'Dis1', 'Art2', 'Fas2', 'Price2', 'Dis2',
        ];
    }

    private function sampleRow(): array
    {
        return [
            'Acapulco Gold',
            'Mexican x Colombian',
            '26-30',
            '1.5%',
            'Feminised',
            '70',
            '30-40',
            'цитрус, кофе',
            'эйфоричный',
            'хвоя',
            '100-150',
            '500-650',
            '60-70',
            '180-220',
            '700-800',
            'September 3rd–4th week',
            'A legendary sativa landrace',
            'Second description block',
            'https://cdn.test/main.jpg',
            'https://cdn.test/one.jpg',
            'https://cdn.test/two.webp',
            '3',
            'ART-3',
            3,
            1500,
            1200,
            'ART-5',
            5,
            2200,
            2000,
        ];
    }
}
