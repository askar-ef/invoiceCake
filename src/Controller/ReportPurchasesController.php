<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Response;
use Cake\ORM\TableRegistry;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use Laminas\Diactoros\Stream;

class ReportPurchasesController extends AppController
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
                return $this->exportExcel($transactions);
            } elseif ($format === 'html') {
                return $this->exportHtml($transactions);
            }
        }
    }

    protected function exportExcel($transactions): Response
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'Customer Name');
        $sheet->setCellValue('B1', 'Transaction Date');
        $sheet->setCellValue('C1', 'Amount');
        $sheet->setCellValue('D1', 'Code');

        // Set data
        $row = 2;
        foreach ($transactions as $transaction) {
            $sheet->setCellValue("A{$row}", $transaction->customer->name);
            $sheet->setCellValue("B{$row}", $transaction->transaction_date->format('Y-m-d H:i:s'));
            $sheet->setCellValue("C{$row}", $transaction->amount);
            $sheet->setCellValue("D{$row}", $transaction->code);
            $row++;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_contents();
        ob_end_clean();

        $response = $this->response->withType('xlsx');
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $excelOutput);
        rewind($stream);

        return $response->withBody(new Stream($stream))
            ->withHeader('Content-Disposition', 'attachment; filename="transactions.xlsx"');
    }

    protected function exportHtml($transactions): Response
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'Customer Name');
        $sheet->setCellValue('B1', 'Transaction Date');
        $sheet->setCellValue('C1', 'Amount');
        $sheet->setCellValue('D1', 'Code');

        // Set data
        $row = 2;
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

        $response = $this->response->withType('text/html');
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $htmlOutput);
        rewind($stream);

        return $response->withBody(new Stream($stream))
            ->withHeader('Content-Disposition', 'attachment; filename="transactions.html"');
    }
}
