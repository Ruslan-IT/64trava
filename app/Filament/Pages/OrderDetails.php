<?php

namespace App\Filament\Pages;

use App\Services\OrderExcelService;
use Filament\Pages\Page;

class OrderDetails extends Page
{
    protected string $view = 'filament.pages.order-details';

    protected static ?string $title = 'Заказ';

    protected static ?string $slug = 'orders/{number}';

    protected static bool $shouldRegisterNavigation = false;

    public array $order = [];

    public function mount(
        string $number,
        OrderExcelService $orderExcelService
    ): void {
        $order = $orderExcelService->find($number);

        abort_if(!$order, 404);

        $this->order = $order;
    }
}
