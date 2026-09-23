<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class CatalogFilter extends Component
{
    use WithPagination;

    public array $seedTypes = [];

    public ?int $thcMin = 1;
    public ?int $thcMax = 60;

    public ?int $floweringMin = 50;
    public ?int $floweringMax = 90;

    public ?string $genotype = null;

    public string $activeThcHandle = '';

    public ?int $heightMin = 70;
    public ?int $heightMax = 200;


    public $currentCategory = null;
    public $brand = null;
    public $tag = null;
    public $catalogInfo = null;

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
        $this->seedTypes = [];

        $this->thcMin = 1;
        $this->thcMax = 60;

        $this->floweringMin = 50;
        $this->floweringMax = 90;

        $this->genotype = null;

        $this->heightMin = 70;
        $this->heightMax = 200;

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
            $query->whereRaw("
                CAST(
                    SUBSTRING_INDEX(
                        REPLACE(thc, '%', ''),
                        '-',
                        -1
                    ) AS DECIMAL(10,2)
                ) BETWEEN ? AND ?
            ", [
                $this->thcMin ?? 0,
                $this->thcMax ?? 100,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Период цветения
        |--------------------------------------------------------------------------
        */

        if ($this->floweringMin !== null || $this->floweringMax !== null) {
            $query->whereRaw("
                CAST(
                    SUBSTRING_INDEX(
                        REPLACE(flowering, ' ', ''),
                        '-',
                        -1
                    ) AS UNSIGNED
                ) BETWEEN ? AND ?
            ", [
                $this->floweringMin ?? 0,
                $this->floweringMax ?? 999,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Генотип
        |--------------------------------------------------------------------------
        |
        | advantages:
        | Сативы 70% / Индики 30%
        |
        */

        if ($this->genotype !== null) {
            $sativa = "
                CAST(
                    SUBSTRING_INDEX(
                        SUBSTRING_INDEX(advantages, 'Сативы ', -1),
                        '%',
                        1
                    ) AS UNSIGNED
                )
            ";

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

        /*
        |--------------------------------------------------------------------------
        | Высота
        |--------------------------------------------------------------------------
        */

        if ($this->heightMin !== null || $this->heightMax !== null) {
            $query->whereRaw("
                CAST(
                    SUBSTRING_INDEX(
                        REPLACE(
                            REPLACE(height, 'см', ''),
                            ' ',
                            ''
                        ),
                        '-',
                        -1
                    ) AS UNSIGNED
                ) BETWEEN ? AND ?
            ", [
                $this->heightMin ?? 0,
                $this->heightMax ?? 999,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Товары
        |--------------------------------------------------------------------------
        */

        $products = $query
            ->latest()
            ->paginate(24);

        /*
        |--------------------------------------------------------------------------
        | Счётчики "Сведение"
        |--------------------------------------------------------------------------
        */

        $seedTypeCounts = [
            'F' => Product::where('seed_type', 'F')->count(),
            'A' => Product::where('seed_type', 'A')->count(),
            'R' => Product::where('seed_type', 'R')->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Счётчики "Генотип"
        |--------------------------------------------------------------------------
        */

        $genotypeCounts = [
            'sativa' => Product::whereRaw("
                CAST(
                    SUBSTRING_INDEX(
                        SUBSTRING_INDEX(advantages, 'Сативы ', -1),
                        '%',
                        1
                    ) AS UNSIGNED
                ) > 50
            ")->count(),

            'balance' => Product::whereRaw("
                CAST(
                    SUBSTRING_INDEX(
                        SUBSTRING_INDEX(advantages, 'Сативы ', -1),
                        '%',
                        1
                    ) AS UNSIGNED
                ) = 50
            ")->count(),

            'indica' => Product::whereRaw("
                CAST(
                    SUBSTRING_INDEX(
                        SUBSTRING_INDEX(advantages, 'Сативы ', -1),
                        '%',
                        1
                    ) AS UNSIGNED
                ) < 50
            ")->count(),
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
            'currentCategory' => $this->currentCategory,
            'brand' => $this->brand,
            'tag' => $this->tag,
            'categories' =>$categories,
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
}
