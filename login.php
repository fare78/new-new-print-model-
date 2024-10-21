<?php
session_start();
include 'db.php'; // Include your database connection file

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        // Login logic
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashed_password);
            $stmt->fetch();
            // After successful login
        if ($stmt->execute()) {
            $stmt->bind_result($user_id, $is_admin);
            $stmt->fetch();

            $_SESSION['user_id'] = $user_id;
            $_SESSION['is_admin'] = $is_admin; // Store is_admin in session

            header("Location: upload.php"); // Redirect to upload page or admin page
            exit();
            }

            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                header("Location: upload.php");
                exit();
            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "No user found with this email.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f7f7f7;
            margin: 0;
        }

        .auth-container {
            width: 300px;
            padding: 30px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        .auth-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4285f4;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .toggle {
            text-align: center;
            margin-top: 10px;
            color: #4285f4;
            cursor: pointer;
        }

        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="auth-container">
        <h2>Login</h2>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
            <div class="toggle">Don't have an account? <a href="signup.php" style="color: #4285f4;">Sign up</a></div>
            <?php if ($error): ?>
                <p class='error'><?= $error ?></p>
            <?php endif; ?>
        </form>
    </div>

</body>
</html>
