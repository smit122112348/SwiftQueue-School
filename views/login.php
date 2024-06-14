<?php
    session_start();
    if (isset($_SESSION['user'])) {
        header("Location: /");
        exit();
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="container mx-auto flex flex-col justify-center items-center gap-5">
        <h1 class="text-4xl font-bold text-center mt-10">Swiftqueue School</h1>
        <div class="w-2/3 p-5 bg-slate-200 rounded-md shadow-lg">
            <h2 class="text-2xl font-bold my-5">Login</h2>
            <form action="/login" method="POST" class="flex flex-col gap-5">
                <input type="email" name="user_email" placeholder="Email" class="p-2 rounded-md" required>
                <input type="password" name="user_password" placeholder="Password" class="p-2 rounded-md" required>
                <button type="submit" class="bg-blue-500 text-white p-2 rounded-md">Login</button>
            </form>
            <?php
                if (isset($_GET['error'])) {
                    echo "<p class='text-red-500'>" . htmlspecialchars($_GET['error']) . "</p>";
                }
            ?>
        </div>
    </div>
</body>
</html>
