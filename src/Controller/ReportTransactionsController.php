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

class ReportTransactionsController extends AppController
{
    public function index()
    {
        if ($this->request->is('post')) {
            $startDate = $this->request->getData('startdate');
            $endDate = $this->request->getData('enddate');
            $format = $this->request->getData('format');

            $transactionsTable = TableRegistry::getTableLocator()->get('Transactions');
            $transactions = $transactionsTable->find()
                ->where(['transaction_date >=' => $startDate, 'transaction_date <=' => $endDate])
                ->contain(['Customers'])
                ->all();

            if ($format === 'excel') {
                return $this->exportExcel($transactions, $startDate, $endDate);
            } elseif ($format === 'html') {
                return $this->exportHtml($transactions, $startDate, $endDate);
            }
        }
    }

    protected function exportExcel($transactions, $startDate, $endDate): Response
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set company name
        $sheet->setCellValue('A1', 'Wahana Artha Group')
            ->mergeCells('A1:D1');
        $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:D1')->getFont()->setBold(true)->setSize(14);

        // set filename
        $sheet->setCellValue('A2', 'Transactions Report')
            ->mergeCells('A2:D2');
        $sheet->getStyle('A2:D2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:D2')->getFont()->setSize(12);

        // Set period
        $sheet->setCellValue('A3', "Period: $startDate to $endDate")
            ->mergeCells('A3:D3');
        $sheet->getStyle('A3:D3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:D3')->getFont()->setItalic(true);

        // Set header
        $sheet->setCellValue('A5', 'Customer Name');
        $sheet->setCellValue('B5', 'Transaction Date');
        $sheet->setCellValue('C5', 'Amount');
        $sheet->setCellValue('D5', 'Code');
        $sheet->getStyle('A5:D5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5:D5')->getFont()->setBold(true);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(16);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(16);

        // Set data
        $row = 6;
        foreach ($transactions as $transaction) {
            $sheet->setCellValue("A{$row}", $transaction->customer->name);
            $sheet->setCellValue("B{$row}", $transaction->transaction_date->format('Y-m-d H:i:s'));
            $sheet->setCellValue("C{$row}", $transaction->amount);
            $sheet->setCellValue("D{$row}", $transaction->code);
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
        $filename = "Transactions_Report_{$formattedStartDate}_to_{$formattedEndDate}.html";

        return $response->withBody(new Stream($stream))
            // ->withHeader('Content-Disposition', 'attachment; filename="transactions.xlsx"');
            ->withHeader('Content-Disposition', "attachment; filename=\"{$filename}\".xlsx");
    }

    protected function exportHtml($transactions, $startDate, $endDate): Response
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set company name
        $sheet->setCellValue('A1', 'Wahana Artha Group')
            ->mergeCells('A1:D1');
        $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:D1')->getFont()->setBold(true)->setSize(14);

        // Set period
        $sheet->setCellValue('A2', "Period: $startDate to $endDate")
            ->mergeCells('A2:D2');
        $sheet->getStyle('A2:D2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:D2')->getFont()->setItalic(true);

        // Set header
        $sheet->setCellValue('A4', 'Customer Name');
        $sheet->setCellValue('B4', 'Transaction Date');
        $sheet->setCellValue('C4', 'Amount');
        $sheet->setCellValue('D4', 'Code');
        $sheet->getStyle('A4:D4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:D4')->getFont()->setBold(true);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(16);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(16);

        // Set data
        $row = 5;
        foreach ($transactions as $transaction) {
            $sheet->setCellValue("A{$row}", $transaction->customer->name);
            $sheet->setCellValue("B{$row}", $transaction->transaction_date->format('Y-m-d H:i:s'));
            $sheet->setCellValue("C{$row}", $transaction->amount);
            $sheet->setCellValue("D{$row}", $transaction->code);
            $row++;
        }

        $writer = new HtmlWriter($spreadsheet);

        ob_start();
        $writer->save('php://output');
        $htmlOutput = ob_get_clean();

        // Format dates for filename
        $formattedStartDate = date('Ymd', strtotime($startDate));
        $formattedEndDate = date('Ymd', strtotime($endDate));
        $filename = "Transactions_Report_{$formattedStartDate}_to_{$formattedEndDate}.html";

        $response = $this->response->withType('text/html');
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $htmlOutput);
        rewind($stream);

        return $response->withBody(new Stream($stream))
            ->withHeader('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
