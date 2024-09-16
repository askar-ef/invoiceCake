$(document).ready(function () {
    // Initialize DataTables
    var transactionsTable = $("#transactionsTable").DataTable();
    var purchasesTable = $("#purchasesTable").DataTable();

    // Function to export data
    function exportData(format) {
        var transactionsData = transactionsTable.buttons.exportData();
        var purchasesData = purchasesTable.buttons.exportData();

        if (format === "csv") {
            var csv = $.csv.fromArrays(transactionsData.header) + "\n";
            csv += $.csv.fromArrays(transactionsData.body) + "\n\n";
            csv += "Purchases\n";
            csv += $.csv.fromArrays(purchasesData.header) + "\n";
            csv += $.csv.fromArrays(purchasesData.body);

            var blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
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
        } else if (format === "excel") {
            // For Excel, we'll use a library like SheetJS (xlsx)
            var wb = XLSX.utils.book_new();
            var ws1 = XLSX.utils.aoa_to_sheet(
                [transactionsData.header].concat(transactionsData.body)
            );
            var ws2 = XLSX.utils.aoa_to_sheet(
                [purchasesData.header].concat(purchasesData.body)
            );
            XLSX.utils.book_append_sheet(wb, ws1, "Transactions");
            XLSX.utils.book_append_sheet(wb, ws2, "Purchases");
            XLSX.writeFile(wb, "company_report.xlsx");
        }
    }

    // Add export buttons
    var exportButtonsContainer = $("#export-buttons");

    $("<button>")
        .text("Export CSV")
        .addClass("btn btn-primary mr-2")
        .click(function () {
            exportData("csv");
        })
        .appendTo(exportButtonsContainer);

    $("<button>")
        .text("Export Excel")
        .addClass("btn btn-success")
        .click(function () {
            exportData("excel");
        })
        .appendTo(exportButtonsContainer);
});
