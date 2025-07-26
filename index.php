<!DOCTYPE html>
<html>
<head>
    <title>Azure SQL Employee Portal</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f0f8ff;
            padding: 30px;
            text-align: center;
        }
        .btn {
            padding: 12px 25px;
            background-color: #0078D4;
            color: white;
            border: none;
            margin: 10px;
            cursor: pointer;
            font-size: 16px;
        }
        input {
            padding: 10px;
            margin: 10px;
            width: 250px;
        }
        table {
            border-collapse: collapse;
            width: 80%;
            margin: 20px auto;
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
    <h1>Azure SQL Employee Portal</h1>

    <!-- Buttons to trigger actions -->
    <form method="post">
        <button class="btn" name="show_form" value="1">Add Employee</button>
        <button class="btn" name="show_list" value="1">Employee List</button>
    </form>

<?php
// Database connection
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

// 1. Insert new employee if form was submitted
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

// 2. Show the employee form
if (isset($_POST['show_form'])) {
    echo '
    <form method="post">
        <h2>Add New Employee</h2>
        <input type="text" name="first_name" placeholder="First Name" required><br>
        <input type="text" name="last_name" placeholder="Last Name" required><br>
        <input type="text" name="department" placeholder="Department" required><br>
        <input class="btn" type="submit" name="submit" value="Save">
    </form>
    ';
}

// 3. Show the employee list (after insert or when requested)
if (isset($_POST['show_list']) || isset($_POST['submit'])) {
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
    } else {
        echo "<p style='color:red;'>❌ Query failed: " . print_r(sqlsrv_errors(), true) . "</p>";
    }
}

sqlsrv_close($conn);
?>
</body>
</html>
