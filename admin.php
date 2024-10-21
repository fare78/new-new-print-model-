<?php
session_start();
include 'db.php'; // Include your database connection file

// Redirect to login if not logged in or not admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

// Fetch orders
$orders = $conn->query("SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id");

// Fetch users for the dropdown
$users = $conn->query("SELECT id, name FROM users");

// Handle adding an order
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_order'])) {
    $userId = $_POST['user_id'];
    $status = 'new';

    // Handle file upload
    if (isset($_FILES['file_path']) && $_FILES['file_path']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'FileUploads/'; // Specify your upload directory
        $uploadFile = $uploadDir . basename($_FILES['file_path']['name']);

        // Move the uploaded file to the specified directory
        if (move_uploaded_file($_FILES['file_path']['tmp_name'], $uploadFile)) {
            // Insert the new order into the database with the file path
            $stmt = $conn->prepare("INSERT INTO orders (user_id, file_path, status, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("iss", $userId, $uploadFile, $status);

            if ($stmt->execute()) {
                echo "<p>Order created successfully!</p>";
            } else {
                echo "<p>Error creating order: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p>Error uploading file.</p>";
        }
    } else {
        echo "<p>No file uploaded or there was an error.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
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

        .button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            text-decoration: none;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .form-container {
            margin: 20px 0;
            text-align: center;
        }

        select,
        input {
            margin: 5px;
            padding: 10px;
            width: 200px;
        }
    </style>
</head>

<body>

    <h1>Admin Panel - Order Management</h1>

    <h2>Add New Order</h2>
    <form method="POST" class="form-container" enctype="multipart/form-data">
        <select name="user_id" required>
            <option value="">Select Customer</option>
            <?php while ($user = $users->fetch_assoc()): ?>
                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
            <?php endwhile; ?>
        </select>

        <input type="file" name="file_path" required>

        <button type="submit" name="add_order" class="button">Add Order</button>
    </form>

    <h2>Order List</h2>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Order Details</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($order = $orders->fetch_assoc()): ?>
                <tr>
                    <td><?= $order['id'] ?></td>
                    <td><?= $order['customer_name'] ?></td>
                    <td><?= htmlspecialchars($order['file_path']) ?></td>
                    <td><?= htmlspecialchars($order['status']) ?></td>
                    <td><?= htmlspecialchars($order['created_at']) ?></td>
                    <td>
                        <a href="order_details.php?order_id=<?= $order['id'] ?>" class="button">details</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>

</html>