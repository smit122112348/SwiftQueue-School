<?php
// This is the login page
@session_start();
if (isset($_SESSION['user'])) {
    header("Location: /");
    exit();
}

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION['csrf_token'];

// Function to generate CSRF token input field
function csrfInput() {
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}
    
?>

<!DOCTYPE html>
<html>
<head>
    <title>Swiftqueue School</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="container mx-auto flex flex-col justify-center items-center gap-5">
        <h1 class="text-4xl font-bold text-center mt-10">Swiftqueue School</h1>
        <div class="w-2/3 p-5 bg-slate-200 rounded-md shadow-lg">
            <div class="flex justify-between my-5">
                <h2 class="text-2xl font-bold">Login</h2>
                <p>Don't have an account? <a href="/register" class="text-blue-500">Register</a></p>
            </div>

            <!-- Check for errors -->
            <?php
                if (isset($_GET['error'])) {
                    echo "<p class='text-red-500'>" . htmlspecialchars($_GET['error']) . "</p>";
                }
            ?>

            <form action="/login" method="POST" class="flex flex-col gap-5">
                <?php echo csrfInput(); ?>
                <input type="email" name="user_email" placeholder="Email" class="p-2 rounded-md" required>
                <input type="password" name="user_password" placeholder="Password" class="p-2 rounded-md" required>
                <button type="submit" class="bg-blue-500 text-white p-2 rounded-md">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
