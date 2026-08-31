<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class ProductSearch extends Component
{
    public $search = '';
    public $products = [];

    public function updatedSearch()
    {
        $search = trim($this->search);

        if (mb_strlen($search) < 2) {
            $this->products = [];

            return;
        }

        $this->products = Product::query()
            ->where('name', 'like', '%' . $search . '%')
            ->get();
    }

    public function render()
    {
        return view('livewire.product-search');
    }
}
