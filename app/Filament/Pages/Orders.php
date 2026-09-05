<?php

namespace App\Filament\Pages;

use App\Services\OrderExcelService;
use Filament\Pages\Page;

class Orders extends Page
{
    protected string $view = 'filament.pages.orders';

    protected static ?string $title = 'Заказы';

    protected static ?string $navigationLabel = 'Заказы';

    protected static ?string $slug = 'orders';

    public array $orders = [];

    public function mount(OrderExcelService $orderExcelService): void
    {
        $this->orders = $orderExcelService->all();
    }

    public function show(
        string $number,
        OrderExcelService $orderExcelService
    ) {
        $order = $orderExcelService->find($number);

        abort_if(!$order, 404);

        return view('filament.pages.order-show', compact('order'));
    }
}


