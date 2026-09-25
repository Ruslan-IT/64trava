<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CatalogFilter extends Component
{
    use WithPagination;

    #[Url(as: 'search', except: '')]
    public string $search = '';

    #[Url(as: 'seed_types', except: [])]
    public array $seedTypes = [];

    #[Url(as: 'thc_min', except: 1)]
    public ?int $thcMin = 1;

    #[Url(as: 'thc_max', except: 60)]
    public ?int $thcMax = 60;

    #[Url(as: 'has_cbd', except: false)]
    public bool $hasCbd = false;

    #[Url(as: 'flowering_min', except: 50)]
    public ?int $floweringMin = 50;

    #[Url(as: 'flowering_max', except: 90)]
    public ?int $floweringMax = 90;

    #[Url(as: 'genotype', except: null)]
    public ?string $genotype = null;

    public string $activeThcHandle = '';

    #[Url(as: 'height_min', except: 70)]
    public ?int $heightMin = 70;

    #[Url(as: 'height_max', except: 200)]
    public ?int $heightMax = 200;

    #[Url(as: 'indoor_height_min', except: 50)]
    public ?int $indoorHeightMin = 50;

    #[Url(as: 'indoor_height_max', except: 250)]
    public ?int $indoorHeightMax = 250;

    #[Url(as: 'indoor_yield_min', except: 100)]
    public ?int $indoorYieldMin = 100;

    #[Url(as: 'indoor_yield_max', except: 1500)]
    public ?int $indoorYieldMax = 1500;

    #[Url(as: 'outdoor_yield_min', except: 100)]
    public ?int $outdoorYieldMin = 100;

    #[Url(as: 'outdoor_yield_max', except: 2500)]
    public ?int $outdoorYieldMax = 2500;

    #[Url(as: 'tastes', except: [])]
    public array $tastes = [];

    #[Url(as: 'effects', except: [])]
    public array $effects = [];

    #[Url(as: 'aromas', except: [])]
    public array $aromas = [];

    #[Url(as: 'harvests', except: [])]
    public array $harvests = [];

    #[Url(as: 'sort', except: '')]
    public string $sort = '';

    public $currentCategory = null;
    public $brand = null;
    public $tag = null;
    public $catalogInfo = null;

    public function updated($property): void
    {
        if ($property === 'page') {
            return;
        }

        $this->resetPage();
    }

    public function updatedSeedTypes(): void
    {
        $this->resetPage();

        $this->dispatch('catalog-filter-changed', filters: [
            'seedTypes' => $this->seedTypes,
            'thcMin' => $this->thcMin,
            'thcMax' => $this->thcMax,
            'floweringMin' => $this->floweringMin,
            'floweringMax' => $this->floweringMax,
            'genotype' => $this->genotype,
        ]);
    }

    public function updatedThcMin(): void
    {
        $this->resetPage();
    }

    public function updatedThcMax(): void
    {
        $this->resetPage();
    }

    public function updatedFloweringMin(): void
    {
        $this->resetPage();
    }

    public function updatedFloweringMax(): void
    {
        $this->resetPage();
    }

    public function updatedGenotype(): void
    {
        $this->resetPage();
    }

    public function updatedHeightMin(): void
    {
        $this->resetPage();
    }

    public function updatedHeightMax(): void
    {
        $this->resetPage();
    }

    public function setThcMin(): void
    {
        $this->activeThcHandle = 'min';
    }

    public function setThcMax(): void
    {
        $this->activeThcHandle = 'max';
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->seedTypes = [];

        $this->thcMin = 1;
        $this->thcMax = 60;
        $this->hasCbd = false;

        $this->floweringMin = 50;
        $this->floweringMax = 90;

        $this->genotype = null;

        $this->heightMin = 70;
        $this->heightMax = 200;

        $this->indoorHeightMin = 50;
        $this->indoorHeightMax = 250;
        $this->indoorYieldMin = 100;
        $this->indoorYieldMax = 1500;
        $this->outdoorYieldMin = 100;
        $this->outdoorYieldMax = 2500;

        $this->tastes = [];
        $this->effects = [];
        $this->aromas = [];
        $this->harvests = [];
        $this->sort = '';

        $this->activeThcHandle = '';

        $this->resetPage();

        $this->dispatch('catalog-filter-changed', filters: [
            'seedTypes' => [],
            'thcMin' => 1,
            'thcMax' => 60,
            'floweringMin' => 50,
            'floweringMax' => 90,
            'genotype' => null,
        ]);
    }

    public function render()
    {
        $query = Product::query()
            ->with(['brand', 'categories', 'tags']);

        if ($this->currentCategory) {
            $query->whereHas('categories', function ($q) {
                $q->where('categories.id', $this->currentCategory->id);
            });
        }

        if ($this->brand) {
            $query->where('brand_id', $this->brand->id);
        }

        if ($this->tag) {
            $query->whereHas('tags', function ($q) {
                $q->where('tags.id', $this->tag->id);
            });
        }

        $search = trim($this->search);

        if ($search !== '') {
            $query->where('name', 'like', '%' . addcslashes($search, '%_\\') . '%');
        }

        /*
        |--------------------------------------------------------------------------
        | Сведение
        |--------------------------------------------------------------------------
        */

        if (!empty($this->seedTypes)) {
            $query->whereIn('seed_type', $this->seedTypes);
        }

        /*
        |--------------------------------------------------------------------------
        | THC
        |--------------------------------------------------------------------------
        */

        if ($this->thcMin !== null || $this->thcMax !== null) {
            $query->whereRaw(
                Product::upperBoundSql('thc') . ' BETWEEN ? AND ?',
                [
                    $this->thcMin ?? 0,
                    $this->thcMax ?? 100,
                ]
            );
        }

        if ($this->hasCbd) {
            $query->whereNotNull('cbd')->where('cbd', '>', 0);
        }

        /*
        |--------------------------------------------------------------------------
        | Период цветения
        |--------------------------------------------------------------------------
        */

        if ($this->floweringMin !== null || $this->floweringMax !== null) {
            $query->whereRaw(
                Product::upperBoundSql('flowering') . ' BETWEEN ? AND ?',
                [
                    $this->floweringMin ?? 0,
                    $this->floweringMax ?? 999,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Генотип
        |--------------------------------------------------------------------------
        |
        | sativa_percent / indica_percent, иначе advantages:
        | Сативы 70% / Индики 30%
        |
        */

        if ($this->genotype !== null) {
            $sativa = Product::sativaValueSql();

            switch ($this->genotype) {
                case 'sativa':
                    $query->whereRaw("$sativa > 50");
                    break;

                case 'balance':
                    $query->whereRaw("$sativa = 50");
                    break;

                case 'indica':
                    $query->whereRaw("$sativa < 50");
                    break;
            }
        }

        $this->applyTokenFilter($query, 'taste', $this->tastes);
        $this->applyTokenFilter($query, 'effect', $this->effects);
        $this->applyTokenFilter($query, 'aroma', $this->aromas);

        if ($this->rangeIsActive($this->indoorHeightMin, $this->indoorHeightMax, 50, 250)) {
            $query->whereRaw(
                Product::upperBoundSql('indoor_height') . ' BETWEEN ? AND ?',
                [
                    $this->indoorHeightMin ?? 0,
                    $this->indoorHeightMax ?? 9999,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Высота Outdoor (существующий фильтр по полю height)
        |--------------------------------------------------------------------------
        */

        if ($this->heightMin !== null || $this->heightMax !== null) {
            $query->whereRaw(
                Product::upperBoundSql('height') . ' BETWEEN ? AND ?',
                [
                    $this->heightMin ?? 0,
                    $this->heightMax ?? 999,
                ]
            );
        }

        if ($this->rangeIsActive($this->indoorYieldMin, $this->indoorYieldMax, 100, 1500)) {
            $query->whereRaw(
                Product::upperBoundSql('yield') . ' BETWEEN ? AND ?',
                [
                    $this->indoorYieldMin ?? 0,
                    $this->indoorYieldMax ?? 99999,
                ]
            );
        }

        if ($this->rangeIsActive($this->outdoorYieldMin, $this->outdoorYieldMax, 100, 2500)) {
            $query->whereRaw(
                Product::upperBoundSql('outdoor_yield') . ' BETWEEN ? AND ?',
                [
                    $this->outdoorYieldMin ?? 0,
                    $this->outdoorYieldMax ?? 99999,
                ]
            );
        }

        if (!empty($this->harvests)) {
            $query->whereIn('harvest', $this->harvests);
        }

        /*
        |--------------------------------------------------------------------------
        | Товары
        |--------------------------------------------------------------------------
        */

        $this->applySort($query);

        $products = $query->paginate(24);

        /*
        |--------------------------------------------------------------------------
        | Счётчики "Сведение"
        |--------------------------------------------------------------------------
        */

        $seedTypeCounts = [
            'F' => Product::where('seed_type', 'F')->count(),
            'A' => Product::where('seed_type', 'A')->count(),
            'R' => Product::where('seed_type', 'R')->count(),
            'AR' => Product::where('seed_type', 'AR')->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Счётчики "Генотип"
        |--------------------------------------------------------------------------
        */

        $sativa = Product::sativaValueSql();

        $genotypeCounts = [
            'sativa' => Product::whereRaw("$sativa > 50")->count(),
            'balance' => Product::whereRaw("$sativa = 50")->count(),
            'indica' => Product::whereRaw("$sativa < 50")->count(),
        ];

        if ($this->currentCategory) {
            $query->whereHas('categories', function ($q) {
                $q->where('categories.id', $this->currentCategory->id);
            });
        }

        if ($this->brand) {
            $query->where('brand_id', $this->brand->id);
        }

        if ($this->tag) {
            $query->whereHas('tags', function ($q) {
                $q->where('tags.id', $this->tag->id);
            });
        }

        $categories = Category::query()
            ->orderBy('name')
            ->get();

        return view('livewire.catalog-filter', [
            'products' => $products,
            'seedTypeCounts' => $seedTypeCounts,
            'genotypeCounts' => $genotypeCounts,
            'cbdCount' => Product::whereNotNull('cbd')->where('cbd', '>', 0)->count(),
            'tasteOptions' => $this->tokenOptions('taste'),
            'effectOptions' => $this->tokenOptions('effect'),
            'aromaOptions' => $this->tokenOptions('aroma'),
            'harvestOptions' => $this->harvestOptions(),
            'sortOptions' => $this->sortOptions(),
            'currentCategory' => $this->currentCategory,
            'brand' => $this->brand,
            'tag' => $this->tag,
            'categories' => $categories,
            'catalogInfo' => $this->catalogInfo,
        ]);
    }

    public function mount($currentCategory = null, $brand = null, $tag = null, $catalogInfo = null): void
    {
        $this->currentCategory = $currentCategory;
        $this->brand = $brand;
        $this->tag = $tag;
        $this->catalogInfo = $catalogInfo;
    }

    protected function rangeIsActive(?int $min, ?int $max, int $defaultMin, int $defaultMax): bool
    {
        return ($min !== null && $min > $defaultMin) || ($max !== null && $max < $defaultMax);
    }

    protected function applyTokenFilter($query, string $column, array $values): void
    {
        $column = preg_replace('/[^a-z_]/', '', $column);

        if ($column === '' || $values === []) {
            return;
        }

        $query->where(function ($inner) use ($column, $values) {
            foreach ($values as $value) {
                $value = trim((string) $value);

                if ($value === '') {
                    continue;
                }

                $inner->orWhereRaw(
                    "FIND_IN_SET(?, REPLACE(REPLACE(REPLACE({$column}, ' и ', ','), ';', ','), ', ', ',')) > 0",
                    [$value]
                );
            }
        });
    }

    protected function applySort($query): void
    {
        $upper = fn (string $column) => Product::upperBoundSql($column);

        $byUpper = function ($query, string $column, string $direction) use ($upper) {
            $expression = $upper($column);
            $query->orderByRaw("({$expression} IS NULL) ASC")
                ->orderByRaw("{$expression} {$direction}");
        };

        switch ($this->sort) {
            case 'name_asc':
                $query->orderBy('name');
                break;

            case 'name_desc':
                $query->orderByDesc('name');
                break;

            case 'thc_asc':
                $byUpper($query, 'thc', 'ASC');
                break;

            case 'thc_desc':
                $byUpper($query, 'thc', 'DESC');
                break;

            case 'cbd_asc':
                $query->orderByRaw('(cbd IS NULL OR cbd = 0) ASC')->orderBy('cbd');
                break;

            case 'cbd_desc':
                $query->orderByRaw('(cbd IS NULL OR cbd = 0) ASC')->orderByDesc('cbd');
                break;

            case 'sativa_asc':
                $expression = Product::sativaValueSql();
                $query->orderByRaw("({$expression} IS NULL) ASC")->orderByRaw("{$expression} ASC");
                break;

            case 'sativa_desc':
                $expression = Product::sativaValueSql();
                $query->orderByRaw("({$expression} IS NULL) ASC")->orderByRaw("{$expression} DESC");
                break;

            case 'indoor_height_asc':
                $byUpper($query, 'indoor_height', 'ASC');
                break;

            case 'indoor_height_desc':
                $byUpper($query, 'indoor_height', 'DESC');
                break;

            case 'indoor_yield_asc':
                $byUpper($query, 'yield', 'ASC');
                break;

            case 'indoor_yield_desc':
                $byUpper($query, 'yield', 'DESC');
                break;

            case 'flowering_asc':
                $byUpper($query, 'flowering', 'ASC');
                break;

            case 'flowering_desc':
                $byUpper($query, 'flowering', 'DESC');
                break;

            case 'outdoor_height_asc':
                $byUpper($query, 'height', 'ASC');
                break;

            case 'outdoor_height_desc':
                $byUpper($query, 'height', 'DESC');
                break;

            case 'outdoor_yield_asc':
                $byUpper($query, 'outdoor_yield', 'ASC');
                break;

            case 'outdoor_yield_desc':
                $byUpper($query, 'outdoor_yield', 'DESC');
                break;

            default:
                $query->latest();
                break;
        }
    }

    protected function tokenOptions(string $column): array
    {
        $counts = [];

        $rows = Product::query()
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->pluck($column);

        foreach ($rows as $row) {
            foreach (Product::splitCharacteristicTokens($row) as $token) {
                $counts[$token] = ($counts[$token] ?? 0) + 1;
            }
        }

        ksort($counts, SORT_NATURAL | SORT_FLAG_CASE);

        return $counts;
    }

    protected function harvestOptions(): array
    {
        return Product::query()
            ->whereNotNull('harvest')
            ->where('harvest', '!=', '')
            ->selectRaw('harvest, COUNT(*) as aggregate')
            ->groupBy('harvest')
            ->orderBy('harvest')
            ->pluck('aggregate', 'harvest')
            ->all();
    }

    protected function sortOptions(): array
    {
        return [
            '' => 'По умолчанию',
            'name_asc' => 'А → Я',
            'name_desc' => 'Я → А',
            'thc_asc' => 'THC ↑',
            'thc_desc' => 'THC ↓',
            'cbd_asc' => 'CBD ↑',
            'cbd_desc' => 'CBD ↓',
            'sativa_asc' => 'Sativa ↑',
            'sativa_desc' => 'Sativa ↓',
            'indoor_height_asc' => 'Indoor Height ↑',
            'indoor_height_desc' => 'Indoor Height ↓',
            'indoor_yield_asc' => 'Indoor Yield ↑',
            'indoor_yield_desc' => 'Indoor Yield ↓',
            'flowering_asc' => 'Flowering: быстрее → дольше',
            'flowering_desc' => 'Flowering: дольше → быстрее',
            'outdoor_height_asc' => 'Outdoor Height ↑',
            'outdoor_height_desc' => 'Outdoor Height ↓',
            'outdoor_yield_asc' => 'Outdoor Yield ↑',
            'outdoor_yield_desc' => 'Outdoor Yield ↓',
        ];
    }
}
