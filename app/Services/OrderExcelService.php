<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class OrderExcelService
{
    private string $directory;

    public function __construct()
    {
        $this->directory = storage_path('app/orders');
    }

    public function save(array $order): string
    {
        if (!is_dir($this->directory)) {
            mkdir($this->directory, 0755, true);
        }

        $spreadsheet = new Spreadsheet();

        $ordersSheet = $spreadsheet->getActiveSheet();
        $ordersSheet->setTitle('Заказ');

        $itemsSheet = $spreadsheet->createSheet();
        $itemsSheet->setTitle('Товары');

        $this->createOrderSheet($ordersSheet, $order);
        $this->createItemsSheet($itemsSheet, $order);

        $date = now()->format('d.m.Y');

        $fileName = $order['number'] . '_' . $date . '.xlsx';

        $filePath = $this->directory . '/' . $fileName;

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return $fileName;
    }

    private function createOrderSheet($sheet, array $order): void
    {
        $sheet->fromArray([
            ['Номер заказа', $order['number']],
            ['Дата', $order['created_at']],
            ['Имя', $order['name']],
            ['Телефон', $order['phone']],
            ['Email', $order['email']],
            ['Город', $order['city']],
            ['Адрес', $order['address']],
            ['Комментарий', $order['comment']],
            ['Способ оплаты', $order['payment_method']],
            ['Промокод', $order['promo_code']],
            ['Сумма товаров', $order['subtotal']],
            ['Скидка', $order['discount']],
            ['Доставка', $order['delivery']],
            ['Итого', $order['total']],
        ], null, 'A1');

        $sheet->getStyle('A1:A14')->getFont()->setBold(true);

        $sheet->getStyle('B11:B14')
            ->getNumberFormat()
            ->setFormatCode('#,##0.00 ₽');

        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(40);
    }

    private function createItemsSheet($sheet, array $order): void
    {
        $sheet->fromArray([[
            'Заказ',
            'Фото',
            'Товар',
            'Вариант',
            'SKU',
            'Количество',
            'Цена',
            'Сумма',
        ]], null, 'A1');

        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        $row = 2;

        foreach ($order['items'] as $item) {

            $sheet->fromArray([[
                $order['number'],
                $item['image'] ?? '',
                $item['product_name'],
                $item['variant_name'],
                $item['sku'],
                $item['quantity'],
                $item['price'],
                $item['total'],
            ]], null, 'A' . $row);

            $sheet->getStyle('G' . $row . ':H' . $row)
                ->getNumberFormat()
                ->setFormatCode('#,##0.00 ₽');

            $sheet->getRowDimension($row)->setRowHeight(90);

            $imagePath = $this->getImagePath($item['image'] ?? null);

            if ($imagePath) {
                $drawing = new Drawing();

                $drawing->setName($item['product_name']);
                $drawing->setDescription($item['product_name']);
                $drawing->setPath($imagePath);
                $drawing->setHeight(80);
                $drawing->setCoordinates('B' . $row);
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setWorksheet($sheet);
            }

            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(35);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);
    }

    private function getImagePath(?string $image): ?string
    {
        if (!$image) {
            return null;
        }

        $image = ltrim($image, '/');

        if (str_starts_with($image, 'storage/')) {
            $image = substr($image, 8);
        }

        $path = storage_path('app/public/' . $image);

        return file_exists($path) ? $path : null;
    }

    public function all(): array
    {
        if (!is_dir($this->directory)) {
            return [];
        }

        $files = glob($this->directory . '/*.xlsx');

        $orders = [];

        foreach ($files as $file) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);

            $sheet = $spreadsheet->getSheetByName('Заказ');

            if (!$sheet) {
                continue;
            }

            $orders[] = [
                'number' => $sheet->getCell('B1')->getValue(),
                'created_at' => $sheet->getCell('B2')->getValue(),
                'name' => $sheet->getCell('B3')->getValue(),
                'phone' => $sheet->getCell('B4')->getValue(),
                'email' => $sheet->getCell('B5')->getValue(),
                'city' => $sheet->getCell('B6')->getValue(),
                'address' => $sheet->getCell('B7')->getValue(),
                'comment' => $sheet->getCell('B8')->getValue(),
                'payment_method' => $sheet->getCell('B9')->getValue(),
                'promo_code' => $sheet->getCell('B10')->getValue(),
                'subtotal' => $sheet->getCell('B11')->getValue(),
                'discount' => $sheet->getCell('B12')->getValue(),
                'delivery' => $sheet->getCell('B13')->getValue(),
                'total' => $sheet->getCell('B14')->getValue(),
                'file' => basename($file),
            ];
        }

        return array_reverse($orders);
    }

    public function find(string $number): ?array
    {
        $files = glob($this->directory . '/' . $number . '_*.xlsx');

        if (!$files) {
            return null;
        }

        $file = $files[0];

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);

        $orderSheet = $spreadsheet->getSheetByName('Заказ');
        $itemsSheet = $spreadsheet->getSheetByName('Товары');

        if (!$orderSheet) {
            return null;
        }

        $order = [
            'number' => $orderSheet->getCell('B1')->getValue(),
            'created_at' => $orderSheet->getCell('B2')->getValue(),
            'name' => $orderSheet->getCell('B3')->getValue(),
            'phone' => $orderSheet->getCell('B4')->getValue(),
            'email' => $orderSheet->getCell('B5')->getValue(),
            'city' => $orderSheet->getCell('B6')->getValue(),
            'address' => $orderSheet->getCell('B7')->getValue(),
            'comment' => $orderSheet->getCell('B8')->getValue(),
            'payment_method' => $orderSheet->getCell('B9')->getValue(),
            'promo_code' => $orderSheet->getCell('B10')->getValue(),
            'subtotal' => $orderSheet->getCell('B11')->getValue(),
            'discount' => $orderSheet->getCell('B12')->getValue(),
            'delivery' => $orderSheet->getCell('B13')->getValue(),
            'total' => $orderSheet->getCell('B14')->getValue(),
            'file' => basename($file),
            'items' => [],
        ];

        if ($itemsSheet) {
            $rows = $itemsSheet->toArray(null, true, true, true);

            array_shift($rows);

            foreach ($rows as $row) {
                if (empty($row['A'])) {
                    continue;
                }

                $order['items'][] = [
                    'number' => $row['A'],
                    'image' => $row['B'],
                    'product_name' => $row['C'],
                    'variant_name' => $row['D'],
                    'sku' => $row['E'],
                    'quantity' => $row['F'],
                    'price' => $row['G'],
                    'total' => $row['H'],
                ];
            }
        }

        return $order;
    }

    public function getFilePath(string $number)
    {
        $files = glob($this->directory . '/' . $number . '_*.xlsx');

        if (!$files) {
            return null;
        }

        return $files[0];
    }


}
