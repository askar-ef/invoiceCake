<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Transaction[] $transactions
 * @var \App\Model\Entity\Purchase[] $purchases
 */
?>
<div class="companyReport index content">
    <h3><?= __('Company Report') ?></h3>

    <div class="mb-3">
        <button id="exportExcel" class="btn btn-primary">Export to Excel</button>
        <button id="exportHTML" class="btn btn-success">Export to HTML</button>
        <button id="exportCSV" class="btn btn-secondary">Export to CSV</button>
    </div>

    <h4><?= __('Transactions') ?></h4>
    <table id="transactionsTable" class="table table-bordered table-hover">
        <thead>
            <tr>
                <th><?= __('ID') ?></th>
                <th><?= __('Customer') ?></th>
                <th><?= __('Date') ?></th>
                <th><?= __('Amount') ?></th>
                <th><?= __('Code') ?></th>
                <th><?= __('Created By') ?></th>
                <th><?= __('Modified By') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?= $this->Number->format($transaction->id) ?></td>
                    <td><?= h($transaction->customer->name) ?></td>
                    <td><?= h($transaction->transaction_date) ?></td>
                    <td><?= $this->Number->format($transaction->amount) ?></td>
                    <td><?= h($transaction->code) ?></td>
                    <td><?= $transaction->createdByUser ? h($transaction->createdByUser->email) : '' ?></td>
                    <td><?= $transaction->modifiedByUser ? h($transaction->modifiedByUser->email) : '' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h4><?= __('Purchases') ?></h4>
    <table id="purchasesTable" class="table table-bordered table-hover">
        <thead>
            <tr>
                <th><?= __('ID') ?></th>
                <th><?= __('Supplier') ?></th>
                <th><?= __('Date') ?></th>
                <th><?= __('Amount') ?></th>
                <th><?= __('Created By') ?></th>
                <th><?= __('Modified By') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($purchases as $purchase): ?>
                <tr>
                    <td><?= $this->Number->format($purchase->id) ?></td>
                    <td><?= h($purchase->supplier->name) ?></td>
                    <td><?= h($purchase->purchase_date) ?></td>
                    <td><?= $this->Number->format($purchase->amount) ?></td>
                    <td><?= $purchase->createdByUser ? h($purchase->createdByUser->email) : '' ?></td>
                    <td><?= $purchase->modifiedByUser ? h($purchase->modifiedByUser->email) : '' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
$this->Html->css('https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css', ['block' => true]);

$this->Html->script('https://code.jquery.com/jquery-3.5.1.js', ['block' => true]);
$this->Html->script('https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js', ['block' => true]);
$this->Html->script('https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js', ['block' => true]);

$this->Html->scriptBlock('
    $(document).ready(function() {
        var transactionsTable = $("#transactionsTable").DataTable();
        var purchasesTable = $("#purchasesTable").DataTable();

        function getTableData(table) {
            return table.data().toArray().map(function(row) {
                return table.columns().header().toArray().map(function(header, index) {
                    return { [header.innerText]: row[index] };
                }).reduce(function(obj, item) {
                    return Object.assign(obj, item);
                }, {});
            });
        }

    $("#exportExcel").click(function() {
        var wb = XLSX.utils.book_new();

        // Create Transaction Sheet
        var transactionsWS = XLSX.utils.json_to_sheet(getTableData(transactionsTable));
        XLSX.utils.book_append_sheet(wb, transactionsWS, "Transactions");

        // Create Purchases Sheet
        var purchasesWS = XLSX.utils.json_to_sheet(getTableData(purchasesTable));
        XLSX.utils.book_append_sheet(wb, purchasesWS, "Purchases");

        // Merging cells and formatting for Transactions sheet
        // Wahana Artha Group Title
        wb.Sheets["Transactions"]["A1"] = { v: "Wahana Artha Group", s: { font: { bold: true, sz: 14 }, alignment: { horizontal: "center" } } };
        wb.Sheets["Transactions"]["!merges"] = [ { s: { r: 0, c: 0 }, e: { r: 0, c: 7 } } ]; // Merge A1:H1

        // Transactions Report Title
        wb.Sheets["Transactions"]["A2"] = { v: "Transactions Report", s: { font: { sz: 12 }, alignment: { horizontal: "center" } } };
        wb.Sheets["Transactions"]["!merges"].push({ s: { r: 1, c: 0 }, e: { r: 1, c: 7 } }); // Merge A2:H2

        // Period
        wb.Sheets["Transactions"]["A3"] = { v: "Period: ", s: { font: { italic: true }, alignment: { horizontal: "center" } } };
        wb.Sheets["Transactions"]["!merges"].push({ s: { r: 2, c: 0 }, e: { r: 2, c: 7 } }); // Merge A3:H3

        // Merging cells and formatting for Purchases sheet
        // Wahana Artha Group Title
        wb.Sheets["Purchases"]["A1"] = { v: "Wahana Artha Group", s: { font: { bold: true, sz: 14 }, alignment: { horizontal: "center" } } };
        wb.Sheets["Purchases"]["!merges"] = [ { s: { r: 0, c: 0 }, e: { r: 0, c: 7 } } ]; // Merge A1:H1

        // Purchases Report Title
        wb.Sheets["Purchases"]["A2"] = { v: "Purchases Report", s: { font: { sz: 12 }, alignment: { horizontal: "center" } } };
        wb.Sheets["Purchases"]["!merges"].push({ s: { r: 1, c: 0 }, e: { r: 1, c: 7 } }); // Merge A2:H2

        // Period
        wb.Sheets["Purchases"]["A3"] = { v: "Period: ", s: { font: { italic: true }, alignment: { horizontal: "center" } } };
        wb.Sheets["Purchases"]["!merges"].push({ s: { r: 2, c: 0 }, e: { r: 2, c: 7 } }); // Merge A3:H3

        // Saving the file
        XLSX.writeFile(wb, "company_report.xlsx");
    });


        $("#exportHTML").click(function() {
            var transactionsHTML = transactionsTable.table().node().outerHTML;
            var purchasesHTML = purchasesTable.table().node().outerHTML;

            var htmlContent = `
                <html>
                    <head>
                        <title>Company Report</title>
                        <style>
                            table { border-collapse: collapse; width: 100%; }
                            table, th, td { border: 1px solid black; }
                            th, td { padding: 8px; text-align: left; }
                            th { background-color: #f2f2f2; }
                        </style>
                    </head>
                    <body>
                        <h1>Wahana Artha Group</h1>
                        <h2>Transactions</h2>
                        ${transactionsHTML}
                        <h2>Purchases</h2>
                        ${purchasesHTML}
                    </body>
                </html>
            `;

            var blob = new Blob([htmlContent], { type: "text/html;charset=utf-8;" });
            var link = document.createElement("a");
            var url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", "company_report.html");
            link.style.visibility = "hidden";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });



        $("#exportCSV").click(function() {
            var transactionsCSV = XLSX.utils.sheet_to_csv(XLSX.utils.json_to_sheet(getTableData(transactionsTable)));
            var purchasesCSV = XLSX.utils.sheet_to_csv(XLSX.utils.json_to_sheet(getTableData(purchasesTable)));
            
            var combinedCSV = "Transactions\n" + transactionsCSV + "\n\nPurchases\n" + purchasesCSV;
            
            var blob = new Blob([combinedCSV], { type: "text/csv;charset=utf-8;" });
            var link = document.createElement("a");
            if (link.download !== undefined) {
                var url = URL.createObjectURL(blob);
                link.setAttribute("href", url);
                link.setAttribute("download", "company_report.csv");
                link.style.visibility = "hidden";
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        });
    });
', ['block' => true]);
?>