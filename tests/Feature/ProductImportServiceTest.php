<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\ProductImportService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Контракт будущего App\Services\ProductImportService.
 * Реализации на этом этапе нет: тесты импорта должны падать.
 *
 * Нормализованная строка использует реальные поля:
 * brand -> brands.name / products.brand_id
 * seed_type F|A|R|AR
 * thc, flowering, indoor_height, height, yield, outdoor_yield хранятся строкой диапазона
 * cbd, sativa_percent, indica_percent — decimal
 * taste, effect, aroma — списки в тех же разделителях, что Product::splitCharacteristicTokens()
 * harvest — готовая строка
 * вариант уникален внутри товара по package_size; sku уникален в product_variants
 *
 * Название: «{сорт} {Feminised|Autoflower|Regular|Autoregular} {бренд}».
 * Повторный импорт не дописывает бренд и тип второй раз.
 * Совпадение товара — по slug этого названия.
 */
class ProductImportServiceTest extends TestCase
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

    public function test_existing_upper_bound_and_sativa_rules_match_catalog(): void
    {
        $brand = Brand::query()->create(['name' => 'Range Lab']);

        $product = Product::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Range Check',
            'price' => 100,
            'thc' => '26-30',
            'flowering' => '60-70',
            'indoor_height' => '100-150',
            'height' => '180-220',
            'yield' => '500-650',
            'outdoor_yield' => '700-800',
            'sativa_percent' => 40,
            'indica_percent' => 60,
            'advantages' => 'Сативы 70% / Индики 30%',
            'taste' => 'цитрус, кофе и хвоя',
            'cbd' => null,
        ]);

        $this->assertSame(30.0, $this->upperBound('thc', $product->id));
        $this->assertSame(70.0, $this->upperBound('flowering', $product->id));
        $this->assertSame(150.0, $this->upperBound('indoor_height', $product->id));
        $this->assertSame(220.0, $this->upperBound('height', $product->id));
        $this->assertSame(650.0, $this->upperBound('yield', $product->id));
        $this->assertSame(800.0, $this->upperBound('outdoor_yield', $product->id));
        $this->assertSame(40.0, $this->sativaValue($product->id));
        $this->assertSame(
            ['цитрус', 'кофе', 'хвоя'],
            Product::splitCharacteristicTokens($product->taste)
        );
    }

    public function test_import_creates_a_new_product(): void
    {
        $product = $this->import($this->row());

        $this->assertInstanceOf(Product::class, $product);
        $this->assertSame(1, Product::query()->count());
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => "Acapulco Gold Feminised Barney's Farm",
            'seed_type' => 'F',
            'price' => 1500,
        ]);
    }

    public function test_reimport_updates_the_same_product_instead_of_duplicating_it(): void
    {
        $created = $this->import($this->row());
        $updated = $this->import($this->row([
            'price' => 1890,
            'thc' => '28',
        ]));

        $this->assertSame($created->id, $updated->id);
        $this->assertSame(1, Product::query()->count());
        $this->assertSame('1890.00', $updated->fresh()->price);
        $this->assertSame(28.0, $this->upperBound('thc', $updated->id));
    }

    public function test_brand_is_created_and_reused_by_name(): void
    {
        $first = $this->import($this->row());
        $second = $this->import($this->row([
            'strain' => 'White Widow',
            'seed_type' => 'A',
            'variants' => [
                ['package_size' => 3, 'sku' => 'WW-A-3', 'price' => 1600, 'stock' => 4],
            ],
        ]));

        $this->assertSame(1, Brand::query()->where('name', "Barney's Farm")->count());
        $this->assertSame($first->brand_id, $second->brand_id);
        $this->assertSame("Barney's Farm", $second->brand->name);
    }

    public function test_existing_brand_is_reused_without_creating_another(): void
    {
        $brand = Brand::query()->create(['name' => "Barney's Farm"]);

        $product = $this->import($this->row());

        $this->assertSame($brand->id, $product->brand_id);
        $this->assertSame(1, Brand::query()->where('name', "Barney's Farm")->count());
    }

    public function test_name_includes_strain_type_and_brand_once(): void
    {
        $product = $this->import($this->row());

        $this->assertSame("Acapulco Gold Feminised Barney's Farm", $product->name);

        $again = $this->import($this->row());

        $this->assertSame("Acapulco Gold Feminised Barney's Farm", $again->fresh()->name);
        $this->assertSame(1, Product::query()->count());
    }

    public function test_name_does_not_duplicate_brand_when_strain_already_contains_it(): void
    {
        $product = $this->import($this->row([
            'strain' => "Acapulco Gold Feminised Barney's Farm",
        ]));

        $this->assertSame("Acapulco Gold Feminised Barney's Farm", $product->name);
        $this->assertSame(1, substr_count($product->name, "Barney's Farm"));
    }

    #[DataProvider('seedTypes')]
    public function test_seed_type_codes_are_stored_and_named(string $code, string $label): void
    {
        $product = $this->import($this->row([
            'seed_type' => $code,
            'variants' => [
                ['package_size' => 3, 'sku' => 'SEED-'.$code.'-3', 'price' => 1500, 'stock' => 1],
            ],
        ]));

        $this->assertSame($code, $product->seed_type);
        $this->assertStringContainsString($label, $product->name);
    }

    public static function seedTypes(): array
    {
        return [
            'feminised' => ['F', 'Feminised'],
            'autoflower' => ['A', 'Autoflower'],
            'regular' => ['R', 'Regular'],
            'autoregular' => ['AR', 'Autoregular'],
        ];
    }

    public function test_thc_single_value_and_range_keep_text_and_upper_bound(): void
    {
        $single = $this->import($this->row(['thc' => '26']));
        $this->assertSame('26', $single->thc);
        $this->assertSame(26.0, $this->upperBound('thc', $single->id));

        $range = $this->import($this->row([
            'strain' => 'Blue Cheese',
            'thc' => '26-30',
            'variants' => [
                ['package_size' => 3, 'sku' => 'BC-F-3', 'price' => 1500, 'stock' => 1],
            ],
        ]));

        $this->assertSame('26-30', $range->thc);
        $this->assertSame(30.0, $this->upperBound('thc', $range->id));
    }

    public function test_empty_cbd_is_not_treated_as_present_and_numeric_cbd_is_stored(): void
    {
        $empty = $this->import($this->row(['cbd' => '']));
        $this->assertNull($empty->fresh()->cbd);
        $this->assertSame(0, Product::query()->where('cbd', '>', 0)->count());

        $zero = $this->import($this->row([
            'strain' => 'Zero Cbd',
            'cbd' => 0,
            'variants' => [
                ['package_size' => 3, 'sku' => 'ZERO-CBD-3', 'price' => 1500, 'stock' => 1],
            ],
        ]));
        $this->assertSame(0, Product::query()->whereKey($zero->id)->where('cbd', '>', 0)->count());

        $present = $this->import($this->row([
            'strain' => 'Has Cbd',
            'cbd' => '1.50',
            'variants' => [
                ['package_size' => 3, 'sku' => 'HAS-CBD-3', 'price' => 1500, 'stock' => 1],
            ],
        ]));
        $this->assertEquals(1.5, (float) $present->fresh()->cbd);
        $this->assertSame(1, Product::query()->whereKey($present->id)->where('cbd', '>', 0)->count());
    }

    public function test_sativa_and_indica_numbers_are_stored_and_range_uses_upper_bound(): void
    {
        $numbers = $this->import($this->row([
            'sativa_percent' => 40,
            'indica_percent' => 60,
        ]));

        $this->assertEquals(40.0, (float) $numbers->sativa_percent);
        $this->assertEquals(60.0, (float) $numbers->indica_percent);
        $this->assertSame(40.0, $this->sativaValue($numbers->id));

        $range = $this->import($this->row([
            'strain' => 'Range Sativa',
            'sativa_percent' => '60-70',
            'indica_percent' => '30-40',
            'variants' => [
                ['package_size' => 3, 'sku' => 'SAT-RANGE-3', 'price' => 1500, 'stock' => 1],
            ],
        ]));

        $this->assertEquals(70.0, (float) $range->fresh()->sativa_percent);
        $this->assertEquals(40.0, (float) $range->fresh()->indica_percent);
        $this->assertSame(70.0, $this->sativaValue($range->id));
    }

    public function test_taste_list_and_string_match_existing_token_filter(): void
    {
        $fromList = $this->import($this->row([
            'taste' => ['цитрус', 'кофе', 'хвоя'],
        ]));

        $this->assertSame(
            ['цитрус', 'кофе', 'хвоя'],
            Product::splitCharacteristicTokens($fromList->taste)
        );
        $this->assertTrue($this->tokenMatches('taste', 'кофе', $fromList->id));

        $fromString = $this->import($this->row([
            'strain' => 'Taste String',
            'taste' => 'цитрус, кофе и хвоя',
            'variants' => [
                ['package_size' => 3, 'sku' => 'TASTE-STR-3', 'price' => 1500, 'stock' => 1],
            ],
        ]));

        $this->assertSame(
            ['цитрус', 'кофе', 'хвоя'],
            Product::splitCharacteristicTokens($fromString->taste)
        );
        $this->assertTrue($this->tokenMatches('taste', 'хвоя', $fromString->id));
    }

    public function test_effect_list_matches_existing_token_filter(): void
    {
        $product = $this->import($this->row([
            'effect' => 'расслабляющий, творческий и эйфоричный',
        ]));

        $this->assertSame(
            ['расслабляющий', 'творческий', 'эйфоричный'],
            Product::splitCharacteristicTokens($product->effect)
        );
        $this->assertTrue($this->tokenMatches('effect', 'эйфоричный', $product->id));
    }

    public function test_aroma_list_matches_existing_token_filter(): void
    {
        $product = $this->import($this->row([
            'aroma' => ['землистый', 'хвоя', 'сладкий'],
        ]));

        $this->assertSame(
            ['землистый', 'хвоя', 'сладкий'],
            Product::splitCharacteristicTokens($product->aroma)
        );
        $this->assertTrue($this->tokenMatches('aroma', 'сладкий', $product->id));
    }

    public function test_flowering_range_keeps_text_and_uses_upper_bound(): void
    {
        $product = $this->import($this->row(['flowering' => '60-70']));

        $this->assertSame('60-70', $product->flowering);
        $this->assertSame(70.0, $this->upperBound('flowering', $product->id));
    }

    public function test_indoor_height_range_uses_upper_bound(): void
    {
        $product = $this->import($this->row(['indoor_height' => '100-150']));

        $this->assertSame('100-150', $product->indoor_height);
        $this->assertSame(150.0, $this->upperBound('indoor_height', $product->id));
    }

    public function test_outdoor_height_range_uses_upper_bound(): void
    {
        $product = $this->import($this->row(['height' => '180-220']));

        $this->assertSame('180-220', $product->height);
        $this->assertSame(220.0, $this->upperBound('height', $product->id));
    }

    public function test_indoor_yield_range_uses_upper_bound(): void
    {
        $product = $this->import($this->row(['yield' => '500-650']));

        $this->assertSame('500-650', $product->yield);
        $this->assertSame(650.0, $this->upperBound('yield', $product->id));
    }

    public function test_outdoor_yield_range_uses_upper_bound(): void
    {
        $product = $this->import($this->row(['outdoor_yield' => '700-800']));

        $this->assertSame('700-800', $product->outdoor_yield);
        $this->assertSame(800.0, $this->upperBound('outdoor_yield', $product->id));
    }

    #[DataProvider('harvestValues')]
    public function test_harvest_string_is_stored_unchanged(string $harvest): void
    {
        $product = $this->import($this->row([
            'strain' => 'Harvest '.$harvest,
            'harvest' => $harvest,
            'variants' => [
                ['package_size' => 3, 'sku' => 'HARVEST-'.md5($harvest), 'price' => 1500, 'stock' => 1],
            ],
        ]));

        $this->assertSame($harvest, $product->fresh()->harvest);
    }

    public static function harvestValues(): array
    {
        return [
            'september' => ['September 3rd–4th week'],
            'october' => ['October 1st–2nd week'],
            'from seed' => ['N days from seed'],
        ];
    }

    public function test_empty_cells_stay_null_and_do_not_abort_import(): void
    {
        $product = $this->import($this->row([
            'cbd' => '',
            'sativa_percent' => '',
            'indica_percent' => null,
            'taste' => [],
            'effect' => '   ',
            'aroma' => null,
            'flowering' => '',
            'indoor_height' => null,
            'height' => '',
            'yield' => null,
            'outdoor_yield' => '',
            'harvest' => null,
        ]));

        $fresh = $product->fresh();

        $this->assertNull($fresh->cbd);
        $this->assertNull($fresh->sativa_percent);
        $this->assertNull($fresh->indica_percent);
        $this->assertNull($fresh->taste);
        $this->assertNull($fresh->effect);
        $this->assertNull($fresh->aroma);
        $this->assertNull($fresh->flowering);
        $this->assertNull($fresh->indoor_height);
        $this->assertNull($fresh->height);
        $this->assertNull($fresh->yield);
        $this->assertNull($fresh->outdoor_yield);
        $this->assertNull($fresh->harvest);
        $this->assertSame(1, Product::query()->count());
    }

    public function test_invalid_seed_type_is_rejected_and_nothing_is_stored(): void
    {
        try {
            $this->import($this->row(['seed_type' => 'FEM']));
            $this->fail('Некорректный тип семян должен останавливать импорт.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('seed_type', $exception->errors());
        }

        $this->assertSame(0, Product::query()->count());
        $this->assertSame(0, ProductVariant::query()->count());
        $this->assertSame(0, Brand::query()->count());
    }

    public function test_variants_are_created_and_updated_by_package_size_without_duplicates(): void
    {
        $product = $this->import($this->row([
            'variants' => [
                ['package_size' => 3, 'sku' => 'ACG-F-3', 'price' => 1500, 'old_price' => null, 'stock' => 10],
                ['package_size' => 5, 'sku' => 'ACG-F-5', 'price' => 2200, 'old_price' => 2400, 'stock' => 4],
            ],
        ]));

        $this->assertSame(2, $product->variants()->count());

        $this->import($this->row([
            'variants' => [
                ['package_size' => 3, 'sku' => 'ACG-F-3', 'price' => 1750, 'stock' => 12],
                ['package_size' => 5, 'sku' => 'ACG-F-5', 'price' => 2300, 'old_price' => 2500, 'stock' => 6],
            ],
        ]));

        $this->assertSame(1, Product::query()->count());
        $this->assertSame(2, ProductVariant::query()->count());

        $three = ProductVariant::query()->where('product_id', $product->id)->where('package_size', 3)->first();
        $five = ProductVariant::query()->where('product_id', $product->id)->where('package_size', 5)->first();

        $this->assertNotNull($three);
        $this->assertNotNull($five);
        $this->assertSame('ACG-F-3', $three->sku);
        $this->assertSame('1750.00', $three->price);
        $this->assertSame(12, $three->stock);
        $this->assertSame('2300.00', $five->price);
        $this->assertSame(6, $five->stock);
    }

    public function test_failed_variant_rolls_back_the_whole_import(): void
    {
        try {
            $this->import($this->row([
                'variants' => [
                    ['package_size' => 3, 'sku' => 'ROLL-3', 'price' => 1500, 'stock' => 10],
                    ['package_size' => 5, 'sku' => 'ROLL-5', 'price' => null, 'stock' => 1],
                ],
            ]));
            $this->fail('Импорт с некорректным вариантом должен завершаться ошибкой.');
        } catch (ValidationException $exception) {
            $this->assertNotEmpty($exception->errors());
        }

        $this->assertSame(0, Product::query()->count());
        $this->assertSame(0, ProductVariant::query()->count());
        $this->assertSame(0, Brand::query()->count());
    }

    public function test_second_import_updates_changed_fields_without_a_duplicate(): void
    {
        $this->import($this->row([
            'thc' => '26-30',
            'harvest' => 'September 3rd–4th week',
            'price' => 1500,
        ]));

        $updated = $this->import($this->row([
            'thc' => '31',
            'harvest' => 'October 1st-2nd week',
            'price' => 2100,
            'cbd' => '0.80',
            'variants' => [
                ['package_size' => 3, 'sku' => 'ACG-F-3', 'price' => 2100, 'stock' => 7],
            ],
        ]));

        $this->assertSame(1, Product::query()->count());
        $this->assertSame(1, ProductVariant::query()->count());
        $this->assertSame('31', $updated->fresh()->thc);
        $this->assertSame('October 1st-2nd week', $updated->fresh()->harvest);
        $this->assertSame('2100.00', $updated->fresh()->price);
        $this->assertEquals(0.8, (float) $updated->fresh()->cbd);
        $this->assertSame(7, $updated->variants()->first()->stock);
        $this->assertSame(1, substr_count($updated->name, "Barney's Farm"));
    }

    private function import(array $row): Product
    {
        return app(ProductImportService::class)->import($row);
    }

    private function row(array $overrides = []): array
    {
        return array_replace([
            'strain' => 'Acapulco Gold',
            'brand' => "Barney's Farm",
            'seed_type' => 'F',
            'price' => 1500,
            'thc' => '26',
            'cbd' => null,
            'sativa_percent' => 70,
            'indica_percent' => 30,
            'taste' => ['цитрус', 'кофе'],
            'effect' => ['эйфоричный'],
            'aroma' => ['хвоя'],
            'flowering' => '65',
            'indoor_height' => '100-150',
            'height' => '180-220',
            'yield' => '500-650',
            'outdoor_yield' => '700-800',
            'harvest' => 'October 1st–2nd week',
            'variants' => [
                ['package_size' => 3, 'sku' => 'ACG-F-3', 'price' => 1500, 'stock' => 10],
            ],
        ], $overrides);
    }

    private function upperBound(string $column, int $productId): float
    {
        $value = DB::table('products')
            ->where('id', $productId)
            ->selectRaw(Product::upperBoundSql($column).' as bound')
            ->value('bound');

        return (float) $value;
    }

    private function sativaValue(int $productId): float
    {
        $value = DB::table('products')
            ->where('id', $productId)
            ->selectRaw(Product::sativaValueSql().' as sativa')
            ->value('sativa');

        return (float) $value;
    }

    private function tokenMatches(string $column, string $token, int $productId): bool
    {
        return DB::table('products')
            ->where('id', $productId)
            ->whereRaw(
                "FIND_IN_SET(?, REPLACE(REPLACE(REPLACE({$column}, ' и ', ','), ';', ','), ', ', ',')) > 0",
                [$token]
            )
            ->exists();
    }
}
