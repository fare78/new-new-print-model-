<?php
session_start();

include 'db.php'; // Include your database connection file

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
date_default_timezone_set('Africa/Cairo');
$message = '';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        $uploadDir = 'FileUploads/'; // Directory where files will be uploaded
        $fileName = basename($file['name']); // Get just the file name
        $filePath = $uploadDir . $fileName; // Create the full file path

        // Check if the upload directory exists; if not, create it
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Move the uploaded file to the specified directory
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Store file information in the uploads table
            $userId = $_SESSION['user_id'];
            $stmt = $conn->prepare("INSERT INTO uploads (user_id, file_path, file_name) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $userId, $filePath, $fileName); // Include the file name in the insert

            // Execute the statement and check for errors
            if ($stmt->execute()) {
                // Also insert a new order into the orders table
                $stmtOrder = $conn->prepare("INSERT INTO orders (user_id, file_path, status, created_at) VALUES (?, ?, 'new', NOW())");
                $stmtOrder->bind_param("is", $userId, $filePath);
                
                if ($stmtOrder->execute()) {
                    $message = "File uploaded successfully!<br>We will call you very soon.";
                } else {
                    $message = "Database insert failed for order: " . $stmtOrder->error;
                }
                
                $stmtOrder->close();
            } else {
                $message = "Database insert failed: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $message = "File upload failed!";
        }
    }
}

// Handle logout action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: welcome.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        input[type="file"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
        }

        button {
            padding: 10px;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .success-msg,
        .error-msg {
            text-align: center;
            font-size: 16px;
            margin-top: 15px;
        }

        .success-msg {
            color: green;
        }

        .error-msg {
            color: red;
        }

        .logout-btn {
            background-color: #dc3545;
            margin-top: 15px;
            text-align: center;
            display: block;
            color: white;
            padding: 10px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Upload Your File</h1>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="file" required>
            <button type="submit">Submit</button>
        </form>
        <!-- Display the message below the form -->
        <?php if (!empty($message)): ?>
            <div class="<?php echo strpos($message, 'successfully') ? 'success-msg' : 'error-msg'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <!-- Logout button -->
        <a class="logout-btn" href="?action=logout">Logout</a>
    </div>
</body>

</html>
