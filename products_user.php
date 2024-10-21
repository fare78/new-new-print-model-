<?php
session_start();
include 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit;
}

// Fetch products from the database
$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <style>
        .upload-form {
            display: none; /* Hide the upload form initially */
        }
        .tab {
            display: none; /* Hide all tabs initially */
        }
        .tab.active {
            display: block; /* Show the active tab */
        }
        #uploadTab {
            margin-top: 20px;
        }
    </style>
    <script>
        function showUploadForm(productId) {
            // Hide all upload forms
            const forms = document.querySelectorAll('.upload-form');
            forms.forEach(form => form.classList.remove('active')); // Hide all forms

            // Show the selected product's upload form
            const activeTab = document.getElementById('upload-form-' + productId);
            activeTab.classList.add('active'); // Show the selected form
        }
    </script>
</head>
<body>

<h1>Products</h1>
<div>
    <h2>Select a Product</h2>
    <select id="productSelect" onchange="showUploadForm(this.value)">
        <option value="">--Choose a product--</option>
        <?php while ($product = mysqli_fetch_assoc($result)): ?>
            <option value="<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></option>
        <?php endwhile; ?>
    </select>
</div>

<div id="uploadTab">
    <?php mysqli_data_seek($result, 0); // Reset the result pointer to the beginning ?>
    <?php while ($product = mysqli_fetch_assoc($result)): ?>
        <div class="upload-form" id="upload-form-<?php echo $product['id']; ?>">
            <h3>Upload for <?php echo htmlspecialchars($product['name']); ?></h3>
            <form action="upload.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <label for="file">Upload your file:</label>
                <input type="file" name="file" required>
                <input type="submit" value="Submit">
            </form>
        </div>
    <?php endwhile; ?>
</div>

<?php
// Free result set and close connection
mysqli_free_result($result);
mysqli_close($conn);
?>

</body>
</html>
