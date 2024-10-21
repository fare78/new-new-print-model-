<?php
session_start();
include 'db.php'; // Include your database connection file

// Redirect to login if not logged in or not admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update order status
    if (isset($_POST['update_status'])) {
        $orderId = $_POST['order_id'];
        $newStatus = $_POST['status'];
        $currentTime = date('Y-m-d H:i:s');

        // Prepare the update query with printed_at and delivered_at logic
        if ($newStatus == 'Printed') {
            $stmt = $conn->prepare("UPDATE orders SET status = ?, printed_at = NOW(), updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("si", $newStatus, $orderId);
        } elseif ($newStatus == 'Delivered') {
            $stmt = $conn->prepare("UPDATE orders SET status = ?, delivered_at = NOW(), updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("si", $newStatus, $orderId);
        } else {
            $stmt = $conn->prepare("UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("si", $newStatus, $orderId);
        }

        $stmt->execute();
        $stmt->close();
    }

    // Cancel order
    if (isset($_POST['cancel_order'])) {
        $orderId = $_POST['order_id'];
        $cancellationReason = $_POST['cancellation_reason'];

        $stmt = $conn->prepare("UPDATE orders SET status = 'Cancelled', cancellation_reason = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $cancellationReason, $orderId);
        $stmt->execute();
        $stmt->close();
    }

    // Add issue
    if (isset($_POST['add_issue'])) {
        $orderId = $_POST['order_id'];
        $issueDescription = $_POST['issue_description'];
        $reason = $_POST['reason'];

        $stmt = $conn->prepare("INSERT INTO order_issues (order_id, issue_description, reason) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $orderId, $issueDescription, $reason);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch orders
$orders = $conn->query("SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id");

// Fetch order issues
$orderIssues = $conn->query("SELECT * FROM order_issues");
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

        .form-container {
            margin: 20px 0;
        }

        .form-container input,
        .form-container select,
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
    </style>
</head>

<body>

    <h1>Admin Panel - Order Management</h1>

    <h2>Order List</h2>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Order Details</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Printed At</th>
                <th>Delivered At</th>
                <th>Execution Time (Printed)</th>
                <th>Execution Time (Delivered)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($order = $orders->fetch_assoc()): ?>
                <tr>
                    <td><?= $order['id'] ?></td>
                    <td><?= $order['customer_name'] ?></td>
                    <td><?= $order['file_path'] ?></td>
                    <td><?= $order['status'] ?></td>
                    <td><?= $order['created_at'] ?></td>
                    <td><?= $order['printed_at'] ? date('Y-m-d H:i:s', strtotime($order['printed_at'])) : 'N/A' ?></td>
                    <td><?= $order['delivered_at'] ? date('Y-m-d H:i:s', strtotime($order['delivered_at'])) : 'N/A' ?></td>
                    <td>
                        <?php
                        if ($order['printed_at']) {
                            $createdAt = new DateTime($order['created_at']);
                            $printedAt = new DateTime($order['printed_at']);
                            $executionTimePrinted = $createdAt->diff($printedAt);
                            echo $executionTimePrinted->format('%h hours %i minutes %s seconds');
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($order['delivered_at']) {
                            $createdAt = new DateTime($order['created_at']);
                            $deliveredAt = new DateTime($order['delivered_at']);
                            $executionTimeDelivered = $printedAt->diff($deliveredAt);
                            echo $executionTimeDelivered->format('%h hours %i minutes %s seconds');
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </td>
                    <td>
                        <!-- Form to update order status -->
                        <form method="POST" class="form-container" style="display:inline-block;">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <select name="status">
                                <option value="Pending" <?= $order['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Printed" <?= $order['status'] == 'Printed' ? 'selected' : '' ?>>Printed</option>
                                <option value="Delivered" <?= $order['status'] == 'Delivered' ? 'selected' : '' ?>>Delivered
                                </option>
                            </select>
                            <button type="submit" name="update_status" class="button">Update</button>
                        </form>

                        <!-- Form to cancel order -->
                        <form method="POST" class="form-container" style="display:inline-block;">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <input type="text" name="cancellation_reason" placeholder="Cancellation Reason" required>
                            <button type="submit" name="cancel_order" class="button">Cancel Order</button>
                        </form>

                        <!-- Form to add an issue -->
                        <form method="POST" class="form-container" style="display:inline-block;">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <input type="text" name="issue_description" placeholder="Issue Description" required>
                            <input type="text" name="reason" placeholder="Reason" required>
                            <button type="submit" name="add_issue" class="button">Add Issue</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2>Order Issues</h2>
    <table>
        <thead>
            <tr>
                <th>Issue ID</th>
                <th>Order ID</th>
                <th>Issue Description</th>
                <th>Reason</th>
                <th>Reported At</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($issue = $orderIssues->fetch_assoc()): ?>
                <tr>
                    <td><?= $issue['id'] ?></td>
                    <td><?= $issue['order_id'] ?></td>
                    <td><?= $issue['issue_description'] ?></td>
                    <td><?= $issue['reason'] ?></td>
                    <td><?= $issue['created_at'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>

</html>