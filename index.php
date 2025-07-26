<!DOCTYPE html>
<html>
<head>
    <title>Employee Form - Azure SQL</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f0f8ff;
            padding: 30px;
            text-align: center;
        }
        input, select {
            padding: 10px;
            margin: 10px;
            width: 250px;
        }
        table {
            border-collapse: collapse;
            width: 80%;
            margin: 30px auto;
            background: white;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #0078D4;
            color: white;
        }
    </style>
</head>
<body>
    <h1>Add Employee</h1>
    <form method="post">
        <input type="text" name="first_name" placeholder="First Name" required><br>
        <input type="text" name="last_name" placeholder="Last Name" required><br>
        <input type="text" name="department" placeholder="Department" required><br>
        <input type="submit" name="submit" value="Add Employee">
    </form>

<?php
// Azure SQL DB connection info
$serverName = "tcp:mydemovm.database.windows.net,1433";
$connectionOptions = array(
    "Database" => "mydemodb",
    "Uid" => "azureadmin",
    "PWD" => "Welcome@123456",
    "Encrypt" => true,
    "TrustServerCertificate" => false
);

$conn = sqlsrv_connect($serverName, $connectionOptions);

if (!$conn) {
    die("<p style='color:red;'>❌ Connection failed: " . print_r(sqlsrv_errors(), true) . "</p>");
}

// Insert data if form is submitted
if (isset($_POST['submit'])) {
    $first = $_POST['first_name'];
    $last = $_POST['last_name'];
    $dept = $_POST['department'];

    $insert = "INSERT INTO Employees (FirstName, LastName, Department) VALUES (?, ?, ?)";
    $params = array($first, $last, $dept);
    $stmt = sqlsrv_query($conn, $insert, $params);

    if ($stmt === false) {
        echo "<p style='color:red;'>❌ Insert failed: " . print_r(sqlsrv_errors(), true) . "</p>";
    } else {
        echo "<p style='color:green;'>✅ Employee added successfully!</p>";
    }
}

// Show all employees
$sql = "SELECT EmployeeID, FirstName, LastName, Department FROM Employees";
$stmt = sqlsrv_query($conn, $sql);

if ($stmt !== false) {
    echo "<h2>Employee List</h2>";
    echo "<table>";
    echo "<tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Department</th></tr>";
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>
                <td>{$row['EmployeeID']}</td>
                <td>{$row['FirstName']}</td>
                <td>{$row['LastName']}</td>
                <td>{$row['Department']}</td>
              </tr>";
    }
    echo "</table>";
    sqlsrv_free_stmt($stmt);
}

sqlsrv_close($conn);
?>
</body>
</html>
