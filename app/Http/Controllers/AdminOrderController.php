<?php

namespace App\Http\Controllers;

use App\Services\OrderExcelService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminOrderController extends Controller
{
    public function show(string $number, OrderExcelService $orderExcelService) {
        $order = $orderExcelService->find($number);

        abort_if(!$order, 404);

        return view('filament.pages.order-show', compact('order'));
    }

    public function download(
        string $number,
        OrderExcelService $orderExcelService
    ): BinaryFileResponse {
        $file = $orderExcelService->getFilePath($number);

        abort_if(!$file, 404);

        return response()->download($file);
    }
}
