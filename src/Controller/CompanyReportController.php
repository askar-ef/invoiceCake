<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use Laminas\Diactoros\Stream;

/**
 * CompanyReports Controller
 */
class CompanyReportController extends AppController
{
    public function apalah() {}
    public function index()
    {
        $this->loadModel('Transactions');
        $this->loadModel('Purchases');

        if ($this->request->is('post')) {
            // Capture the start and end dates from the form
            $startDate = $this->request->getData('start_date');
            $endDate = $this->request->getData('end_date');

            // Fetch transactions and purchases within the date range
            $transactions = $this->Transactions->find('all')
                ->where(['transaction_date >=' => $startDate, 'transaction_date <=' => $endDate])
                ->contain(['Customers', 'CreatedByUsers', 'ModifiedByUsers']);

            $purchases = $this->Purchases->find('all')
                ->where(['purchase_date >=' => $startDate, 'purchase_date <=' => $endDate])
                ->contain(['Suppliers', 'CreatedByUsers', 'ModifiedByUsers']);

            if ($this->request->getData('exportExcel') !== null) {
                return $this->exportExcelReport($transactions, $purchases, $startDate, $endDate);
            }

            if ($this->request->getData('exportHtml') !== null) {
                return $this->exportHtmlReport($transactions, $purchases, $startDate, $endDate);
            }
        }

        // Default: fetch all transactions and purchases if no date is selected
        $transactions = $this->Transactions->find('all')->contain(['Customers', 'CreatedByUsers', 'ModifiedByUsers']);
        $purchases = $this->Purchases->find('all')->contain(['Suppliers', 'CreatedByUsers', 'ModifiedByUsers']);

        $this->set(compact('transactions', 'purchases'));
    }

    protected function exportExcelReport($transactions, $purchases, $startDate, $endDate): Response
    {
        $spreadsheet = new Spreadsheet();

        // Transactions sheet setup
        $transactionsSheet = $spreadsheet->getActiveSheet();
        $transactionsSheet->setTitle('Transactions');
        $this->setCompanyHeader($transactionsSheet, $startDate, $endDate, 'Transactions Report');

        // Set headers
        $transactionsSheet->setCellValue('A5', 'No');
        $transactionsSheet->setCellValue('B5', 'Customer Name');
        $transactionsSheet->setCellValue('C5', 'Transaction Date');
        $transactionsSheet->setCellValue('D5', 'Amount');
        $transactionsSheet->setCellValue('E5', 'Code');
        $transactionsSheet->getStyle('A5:E5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $transactionsSheet->getStyle('A5:E5')->getFont()->setBold(true);

        $transactionsSheet->getColumnDimension('A')->setWidth(3);
        $transactionsSheet->getColumnDimension('B')->setWidth(15);
        $transactionsSheet->getColumnDimension('C')->setWidth(16);
        $transactionsSheet->getColumnDimension('D')->setWidth(12);
        $transactionsSheet->getColumnDimension('E')->setWidth(12);

        // Add transaction data
        $row = 6;
        $count = 1;
        foreach ($transactions as $transaction) {
            $transactionsSheet->setCellValue("A{$row}", $count);
            $transactionsSheet->setCellValue("B{$row}", $transaction->customer->name);
            $transactionsSheet->setCellValue("C{$row}", $transaction->transaction_date->format('Y-m-d'));
            $transactionsSheet->setCellValue("D{$row}", $transaction->amount);
            $transactionsSheet->setCellValue("E{$row}", $transaction->code);
            $row++;
            $count++;
        }

        // Create a new sheet for purchases
        $purchasesSheet = $spreadsheet->createSheet();
        $purchasesSheet->setTitle('Purchases');
        $this->setCompanyHeader($purchasesSheet, $startDate, $endDate, 'Purchases Report');

        // Set headers for purchases
        $purchasesSheet->setCellValue('A5', 'No');
        $purchasesSheet->setCellValue('B5', 'Supplier Name');
        $purchasesSheet->setCellValue('C5', 'Purchase Date');
        $purchasesSheet->setCellValue('D5', 'Amount');
        $purchasesSheet->setCellValue('E5', 'Created By');
        $purchasesSheet->setCellValue('F5', 'Modified By');
        $purchasesSheet->getStyle('A5:F5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $purchasesSheet->getStyle('A5:F5')->getFont()->setBold(true);

        $purchasesSheet->getColumnDimension('A')->setWidth(3);
        $purchasesSheet->getColumnDimension('B')->setWidth(16);
        $purchasesSheet->getColumnDimension('C')->setWidth(16);
        $purchasesSheet->getColumnDimension('D')->setWidth(12);
        $purchasesSheet->getColumnDimension('E')->setWidth(20);
        $purchasesSheet->getColumnDimension('F')->setWidth(20);

        // Add purchases data
        $row = 6;
        $count = 1;
        foreach ($purchases as $purchase) {
            $purchasesSheet->setCellValue("A{$row}", $count);
            $purchasesSheet->setCellValue("B{$row}", $purchase->supplier->name);
            $purchasesSheet->setCellValue("C{$row}", ($purchase->purchase_date)->format('Y-m-d'));
            $purchasesSheet->setCellValue("D{$row}", $purchase->amount);
            $purchasesSheet->setCellValue("E{$row}", $purchase->createdByUser ? $purchase->createdByUser->email : '');
            $purchasesSheet->setCellValue("F{$row}", $purchase->modifiedByUser ? $purchase->modifiedByUser->email : '');
            $row++;
            $count++;
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
        $filename = "Company_Report_{$formattedStartDate}_to_{$formattedEndDate}.xlsx";

        return $response->withBody(new Stream($stream))
            ->withHeader('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    protected function exportHtmlReport($transactions, $purchases, $startDate, $endDate): Response
    {
        // HTML export logic
        $html = '<html><head><style>table {border-collapse: collapse; width: 100%;} table, th, td {border: 1px solid black;} th, td {padding: 8px; text-align: left;} th {background-color: #f2f2f2;} </style></head><body>';
        $html .= '<h1>Wahana Artha Group</h1>';
        $html .= '<h2>Transactions Report (Period: ' . $startDate . ' to ' . $endDate . ')</h2>';

        // Transactions data
        $html .= '<table><thead><tr>';
        $html .= '<th>Customer Name</th><th>Transaction Date</th><th>Amount</th><th>Code</th><th>Created By</th><th>Modified By</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($transactions as $transaction) {
            $html .= '<tr>';
            $html .= '<td>' . $transaction->customer->name . '</td>';
            $html .= '<td>' . $transaction->transaction_date->format('Y-m-d') . '</td>';
            $html .= '<td>' . $transaction->amount . '</td>';
            $html .= '<td>' . $transaction->code . '</td>';
            $html .= '<td>' . ($transaction->createdByUser ? $transaction->createdByUser->email : '') . '</td>';
            $html .= '<td>' . ($transaction->modifiedByUser ? $transaction->modifiedByUser->email : '') . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        // Purchases data
        $html .= '<h2>Purchases Report (Period: ' . $startDate . ' to ' . $endDate . ')</h2>';
        $html .= '<table><thead><tr>';
        $html .= '<th>Supplier Name</th><th>Purchase Date</th><th>Amount</th><th>Created By</th><th>Modified By</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($purchases as $purchase) {
            $html .= '<tr>';
            $html .= '<td>' . $purchase->supplier->name . '</td>';
            $html .= '<td>' . $purchase->purchase_date->format('Y-m-d') . '</td>';
            $html .= '<td>' . $purchase->amount . '</td>';
            $html .= '<td>' . ($purchase->createdByUser ? $purchase->createdByUser->email : '') . '</td>';
            $html .= '<td>' . ($purchase->modifiedByUser ? $purchase->modifiedByUser->email : '') . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        $html .= '</body></html>';

        // Return HTML file as response
        $response = $this->response->withType('text/html');
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $html);
        rewind($stream);

        $formattedStartDate = date('Ymd', strtotime($startDate));
        $formattedEndDate = date('Ymd', strtotime($endDate));
        $filename = "Company_Report_{$formattedStartDate}_to_{$formattedEndDate}.html";

        return $response->withBody(new Stream($stream))
            ->withHeader('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    // Helper method to set company headers for Excel
    protected function setCompanyHeader($sheet, $startDate, $endDate, $reportTitle)
    {
        $sheet->setCellValue('A1', 'Wahana Artha Group')
            ->mergeCells('A1:F1');
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:F1')->getFont()->setBold(true)->setSize(14);

        $sheet->setCellValue('A2', $reportTitle)
            ->mergeCells('A2:F2');
        $sheet->getStyle('A2:F2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:F2')->getFont()->setSize(12);

        $sheet->setCellValue('A3', "Period: $startDate to $endDate")
            ->mergeCells('A3:F3');
        $sheet->getStyle('A3:F3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:F3')->getFont()->setItalic(true);
    }
}
