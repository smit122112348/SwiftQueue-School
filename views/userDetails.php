<?php
// This is the view file for user details

// Check if the user is logged in
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /login");
    exit();
}
require_once 'models/User.php';

$conn = require 'db.php'; // Get the database connection

// Get all users
if ($conn) {
    $userObj = new User($conn);
    $users = $userObj->getAllUsers();
}

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get the CSRF token
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
    <script>
        // Function to handle making a user admin
        function handleMakeAdmin(event, userId) {
            event.preventDefault();
            
            // Make a POST request to make the user as admin
            fetch('/makeAdmin', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?php echo $csrf_token ?>' // Add CSRF token to headers
                },
                body: JSON.stringify({ userId: userId })
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Make admin failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // Function to handle blocking/unblocking a user
        function handleUserAccess(event, userId, userType) {
            event.preventDefault();
            
            // Make a POST request to block/unblock the user
            fetch('/userAccess', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?php echo $csrf_token ?>' // Add CSRF token to headers
                },
                body: JSON.stringify({ userId: userId, userType: userType})
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Block user failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // Function to handle logout
        function handleLogout(event) {
            event.preventDefault();
            
            // Make a POST request to logout the user
            fetch('/logout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?php echo $csrf_token ?>' // Add CSRF token to headers
                }
            })
            .then(response => {
                if (response.ok) {
                    window.location.href = '/login';
                } else {
                    alert('Logout failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // Function to handle delete account
        function handleDeleteAccount(event) {
            event.preventDefault();
            
            // Make a DELETE request to delete the account
            fetch('/deleteAccount', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?php echo $csrf_token ?>' // Add CSRF token to headers
                }
            })
            .then(response => {
                if (response.ok) {
                    window.location.href = '/login';
                } else {
                    alert('Delete account failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
</head>
<body>
    <div class="container mx-auto flex flex-col justify-center items-center gap-5">
        <h1 class="text-4xl font-bold text-center mt-10">Swiftqueue School</h1>
        <div class="w-2/3 p-5 bg-slate-200 rounded-md shadow-lg">
            <div class="flex justify-between my-5">
                <h2 class="text-2xl font-bold">User Details</h2>
                <div>
                    <a href="/" class="bg-blue-500 text-white p-2 rounded-md">Home</a>
                    <button id="logout-btn" class="bg-red-500 text-white p-2 rounded-md" onclick="handleLogout(event)">Logout</button>
                </div>
            </div>
            <div class="flex flex-col gap-5">
                <div class="flex gap-2">
                    <p class="font-bold">Full Name: </p>
                    <p><?php echo htmlspecialchars($_SESSION['user']['user_full_name']); ?></p>
                </div>
                <div class="flex gap-2">
                    <p class="font-bold">Email: </p>
                    <p><?php echo htmlspecialchars($_SESSION['user']['user_email']); ?></p>
                </div>
                <div class="flex gap-2">
                    <p class="font-bold">User Type: </p>
                    <p><?php echo htmlspecialchars($_SESSION['user']['user_type']); ?></p>
                </div>
                <button id="delete-account-btn" class="bg-red-500 text-white p-2 rounded-md w-fit shadow-md" onclick="handleDeleteAccount(event)">Delete Account</button>
            </div>
        </div>

        <!-- Only Admin can make any other user admin and can block other user -->
        <?php if ($_SESSION['user']['user_type'] === 'admin') { ?>

            <div class="w-2/3 p-5 bg-slate-200 rounded-md shadow-lg mt-5">
                <h2 class="text-2xl font-bold">Admin Actions</h2>
                <div class="flex flex-col gap-5">
                    <div class="flex flex-col gap-2">
                        <h3 class="text-xl font-bold">Users</h3>

                        <!-- list of all users -->
                        <?php foreach ($users as $user):
                            if ($user['user_id'] === $_SESSION['user']['user_id']) {
                                continue;
                            }
                        ?>

                            <div class="flex gap-2 items-center">
                                <p><?php echo htmlspecialchars($user['user_full_name']); ?> ||</p>
                                <p><?php echo htmlspecialchars($user['user_email']); ?> ||</p>
                                <p><?php echo htmlspecialchars($user['user_type']); ?></p>

                                <!-- Check the user type -->
                                <?php if ($user['user_type'] !== 'admin'): ?>

                                    <button id="make-admin-btn" class="bg-blue-500 text-white p-2 rounded-md" onclick="handleMakeAdmin(event, <?= $user['user_id'] ?>)">Make Admin</button>
                                    <button id="block-user-btn" class="bg-red-500 text-white p-2 rounded-md" onclick="handleUserAccess(event, <?= $user['user_id'] ?>, '<?= htmlspecialchars($user['user_type']) ?>')">
                                        
                                        <!-- Set Button Text according to the user type -->
                                        <?php echo $user['user_type'] === 'blocked' ? 'Unblock' : 'Block'; ?>
                                    
                                    </button>

                                <?php endif; ?>

                            </div>

                        <?php endforeach; ?>

                    </div>
                </div>
            </div>

        <?php } ?>

    </div>
</body>
</html>
