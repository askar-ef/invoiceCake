<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Response;
use Cake\ORM\TableRegistry;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Laminas\Diactoros\Stream;

class ReportPurchasesController extends AppController
{
    public function index()
    {
        if ($this->request->is('post')) {
            $startDate = $this->request->getData('startdate');
            $endDate = $this->request->getData('enddate');
            $format = $this->request->getData('format');

            $purchasesTable = TableRegistry::getTableLocator()->get('Purchases');
            $purchases = $purchasesTable->find()
                ->where(['purchase_date >=' => $startDate, 'purchase_date <=' => $endDate])
                ->contain(['Suppliers'])
                ->all();

            if ($format === 'excel') {
                return $this->exportExcel($purchases, $startDate, $endDate);
            } elseif ($format === 'html') {
                return $this->exportHtml($purchases, $startDate, $endDate);
            }
        }
    }

    protected function exportExcel($purchases, $startDate, $endDate): Response
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set company name
        $sheet->setCellValue('A1', 'Wahana Artha Group')
            ->mergeCells('A1:C1');
        $sheet->getStyle('A1:C1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:C1')->getFont()->setBold(true)->setSize(14);

        // set filename
        $sheet->setCellValue('A2', 'Purchases Report')
            ->mergeCells('A2:C2');
        $sheet->getStyle('A2:C2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:C2')->getFont()->setSize(12);

        // Set period
        $sheet->setCellValue('A3', "Period: $startDate to $endDate")
            ->mergeCells('A3:C3');
        $sheet->getStyle('A3:C3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:C3')->getFont()->setItalic(true);

        // Set header
        $sheet->setCellValue('A5', 'Customer Name');
        $sheet->setCellValue('B5', 'Purchase Date');
        $sheet->setCellValue('C5', 'Amount');
        $sheet->getStyle('A5:C5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5:C5')->getFont()->setBold(true);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(12);

        // Set data
        $row = 6;
        foreach ($purchases as $purchase) {
            $sheet->setCellValue("A{$row}", $purchase->supplier->name);
            $sheet->setCellValue("B{$row}", $purchase->purchase_date->format('Y-m-d H:i:s'));
            $sheet->setCellValue("C{$row}", $purchase->amount);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        $response = $this->response->withType('xlsx');
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $excelOutput);
        rewind($stream);

        $formattedStartDate = date('Ymd', strtotime($startDate));
        $formattedEndDate = date('Ymd', strtotime($endDate));
        $filename = "Purchases_Report_{$formattedStartDate}_to_{$formattedEndDate}.html";

        return $response->withBody(new Stream($stream))
            // ->withHeader('Content-Disposition', 'attachment; filename="purchases.xlsx"');
            ->withHeader('Content-Disposition', "attachment; filename=\"{$filename}\".xlsx");
    }

    protected function exportHtml($purchases, $startDate, $endDate): Response
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set company name
        $sheet->setCellValue('A1', 'Wahana Artha Group')
            ->mergeCells('A1:C1');
        $sheet->getStyle('A1:C1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:C1')->getFont()->setBold(true)->setSize(14);

        // Set period
        $sheet->setCellValue('A2', "Period: $startDate to $endDate")
            ->mergeCells('A2:C2');
        $sheet->getStyle('A2:C2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:C2')->getFont()->setItalic(true);

        // Set header
        $sheet->setCellValue('A4', 'Customer Name');
        $sheet->setCellValue('B4', 'Purchase Date');
        $sheet->setCellValue('C4', 'Amount');
        $sheet->getStyle('A4:C4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:C4')->getFont()->setBold(true);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);

        // Set data
        $row = 5;
        foreach ($purchases as $purchase) {
            $sheet->setCellValue("A{$row}", $purchase->supplier->name);
            $sheet->setCellValue("B{$row}", $purchase->purchase_date->format('Y-m-d H:i:s'));
            $sheet->setCellValue("C{$row}", $purchase->amount);
            $row++;
        }

        $writer = new HtmlWriter($spreadsheet);

        ob_start();
        $writer->save('php://output');
        $htmlOutput = ob_get_clean();

        // Format dates for filename
        $formattedStartDate = date('Ymd', strtotime($startDate));
        $formattedEndDate = date('Ymd', strtotime($endDate));
        $filename = "Purchases_Report_{$formattedStartDate}_to_{$formattedEndDate}.html";

        $response = $this->response->withType('text/html');
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $htmlOutput);
        rewind($stream);

        return $response->withBody(new Stream($stream))
            ->withHeader('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
