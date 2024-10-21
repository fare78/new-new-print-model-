<?php
session_start();
include 'db.php'; // Include your database connection file

// Redirect to login if not logged in or not admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

// Handle adding a printing center
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_printing_center'])) {
    $name = $_POST['name'];
    $contactNumber = $_POST['contact_number'];
    $product = $_POST['product'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("INSERT INTO printing_centers (name, contact_number, product, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $name, $contactNumber, $product, $price);
    $stmt->execute();
    $stmt->close();
}

// Handle adding a delivery center
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_delivery_center'])) {
    $name = $_POST['name'];
    $contactNumber = $_POST['contact_number'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("INSERT INTO delivery_centers (name, contact_number, price) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $name, $contactNumber, $price);
    $stmt->execute();
    $stmt->close();
}

// Fetch printing centers
$printingCenters = $conn->query("SELECT * FROM printing_centers");

// Fetch delivery centers
$deliveryCenters = $conn->query("SELECT * FROM delivery_centers");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Centers</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }

        h1 {
            color: #333;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        .form-container {
            margin: 20px 0;
        }

        .form-container input,
        .form-container button {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .button {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .back-button {
            margin-bottom: 20px;
            /* Space between button and form */
            display: block;
            /* Center the button */
            text-align: center;
        }
    </style>
</head>

<body>

    <h1>Manage Printing and Delivery Centers</h1>

    <div class="back-button">
        <a href="admin.php" class="button">Back to Admin Panel</a>
    </div>

    <h2>Add Printing Center</h2>
    <form method="POST" class="form-container">
        <input type="text" name="name" placeholder="Center Name" required>
        <input type="text" name="contact_number" placeholder="Contact Number" required>
        <input type="text" name="product" placeholder="Product" required>
        <input type="number" step="0.01" name="price" placeholder="Price" required>
        <button type="submit" name="add_printing_center" class="button">Add Printing Center</button>
    </form>

    <h2>Printing Centers</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Contact Number</th>
                <th>Product</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($center = $printingCenters->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($center['name']) ?></td>
                    <td><?= htmlspecialchars($center['contact_number']) ?></td>
                    <td><?= htmlspecialchars($center['product']) ?></td>
                    <td><?= htmlspecialchars($center['price']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2>Add Delivery Center</h2>
    <form method="POST" class="form-container">
        <input type="text" name="name" placeholder="Center Name" required>
        <input type="text" name="contact_number" placeholder="Contact Number" required>
        <input type="number" step="0.01" name="price" placeholder="Price" required>
        <button type="submit" name="add_delivery_center" class="button">Add Delivery Center</button>
    </form>

    <h2>Delivery Centers</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Contact Number</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($center = $deliveryCenters->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($center['name']) ?></td>
                    <td><?= htmlspecialchars($center['contact_number']) ?></td>
                    <td><?= htmlspecialchars($center['price']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>

</html>