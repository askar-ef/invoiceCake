<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Supplier</title>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
</head>

<body>

    <div class="container mt-5">
        <h2>Data Supplier</h2>
        <table id="example" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Supplier Name</th>
                    <th>Contact</th>
                    <th>Location</th>
                    <th>Joined Date</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Supplier A</td>
                    <td>0123-456789</td>
                    <td>New York</td>
                    <td>2021-01-10</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Supplier B</td>
                    <td>0987-654321</td>
                    <td>San Francisco</td>
                    <td>2021-03-15</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Supplier C</td>
                    <td>1234-567890</td>
                    <td>Los Angeles</td>
                    <td>2021-06-20</td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>Supplier D</td>
                    <td>4321-098765</td>
                    <td>Chicago</td>
                    <td>2021-09-05</td>
                </tr>
                <tr>
                    <td>5</td>
                    <td>Supplier E</td>
                    <td>2345-678901</td>
                    <td>Houston</td>
                    <td>2021-11-25</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Load jQuery and DataTables JS -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <!-- Initialize DataTables -->
    <script type="text/javascript">
        $(document).ready(function() {
            $('#example').DataTable();
        });
    </script>

</body>

</html>


</html>