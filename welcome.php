<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Printing Service</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e9ecef;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .welcome-container {
            text-align: center;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 400px;
            width: 100%;
        }

        h1 {
            color: #343a40;
            margin-bottom: 20px;
        }

        p {
            color: #6c757d;
            margin-bottom: 30px;
        }

        .button {
            background-color: #007BFF;
            color: white;
            padding: 15px 25px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin: 5px 0;
            display: inline-block;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .whatsapp-button {
            background-color: #25D366;
            /* WhatsApp color */
        }

        .whatsapp-button:hover {
            background-color: #20b358;
        }
    </style>
</head>

<body>
    <div class="welcome-container">
        <h1>We Print &amp; We Deliver!</h1>
        <p>Your files will be printed and delivered right to your home.</p>
        <p>Please log in or sign up to get started!</p>
        <a href="login.php" class="button">Login</a> 
        <a href="signup.php" class="button">Sign Up</a>
        <br>
        <br>
        
        <a href="https://wa.me/201151222468" target="_blank" class="button whatsapp-button">Or Chat with us on
            WhatsApp</a>
    </div>
</body>

</html>