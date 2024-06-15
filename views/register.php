<?php 
    session_start();
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
                <h2 class="text-2xl font-bold">Register</h2>
                <p>Already have an account? <a href="/login" class="text-blue-500">Login</a></p>
            </div>
            <?php
                if (isset($_GET['error'])) {
                    echo "<p class='text-red-500'>" . htmlspecialchars($_GET['error']) . "</p>";
                }
            ?>
            <form action="/register" method="POST" class="flex flex-col gap-5">
                <?php echo csrfInput(); ?>
                <div class="flex flex-col gap-1">
                    <label for="full-name">Full Name:</label>
                    <input type="text" id="full-name" name="full-name" class="p-2 rounded-md border border-gray-300" required>
                </div>
                <div class="flex flex-col gap-1">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="p-2 rounded-md border border-gray-300" required>
                </div>
                <div class="flex flex-col gap-1">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" class="p-2 rounded-md border border-gray-300" required>
                </div>
                <div class="flex flex-col gap-1">
                    <label for="confirm-password">Confirm Password:</label>
                    <input type="password" id="confirm-password" name="confirm-password" class="p-2 rounded-md border border-gray-300" required>
                </div>
                <button type="submit" class="bg-blue-500 text-white p-2 rounded-md">Register</button>
            </form>
        </div>
    </div>
</body>
</html>