<?php
// This is the view file for editing a course
@session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /login");
    exit();
}
$config = require 'config.php';
require_once 'models/Course.php';

$conn = require 'db.php'; // Get the database connection

$coursesObj = new Course($conn, $config['DB_NAME']);
$course = $coursesObj->getCourseDetails($_GET['courseId']);

// Check if course exists
if (!$course) {
    // Show 404.php
    include '404.php';
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
    <script>
        // function to handle logout
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
    </script>
</head>
<body>
    <div class="container mx-auto flex flex-col justify-center items-center gap-5">
        <h1 class="text-4xl font-bold text-center mt-10">Swiftqueue School</h1>
        <div class="w-2/3 p-5 bg-slate-200 rounded-md shadow-lg">
            <div class="flex justify-between">
                <a href="/" class="bg-blue-500 text-white p-2 rounded-md">Home</a>
                <button id="logout-btn" class="bg-red-500 text-white p-2 rounded-md" onclick="handleLogout(event)">Logout</button>
            </div>
            <h2 class="text-2xl font-bold my-5">Edit Course</h2>
            <form action="/editCourse" method="POST" class="flex flex-col gap-5">
                <?php echo csrfInput(); ?>
                <input type="hidden" name="_method" value="PUT">
                <input class="p-2 rounded-md border border-gray-300" type="hidden" name="course-id" value="<?php echo htmlspecialchars($course['course_id']); ?>">

                <div class="flex flex-col gap-1">
                    <label for="name">Course Name</label>
                    <input class="p-2 rounded-md border border-gray-300" type="text" name="course-name" id="name" value="<?php echo htmlspecialchars($course['course_name']); ?>">
                </div>

                <div class="flex flex-col gap-1">
                    <label for="description">Description</label>
                    <textarea name="course-description" id="description" class="p-2 rounded-md border border-gray-300"><?php echo htmlspecialchars($course['course_description']); ?></textarea>
                </div>

                <div class="flex gap-1">
                    <div class="flex flex-col gap-1 flex-1">
                        <label for="startDate">Start Date</label>
                        <input class="p-2 rounded-md border border-gray-300" type="date" name="course-start-date" id="startDate" value="<?php echo date('Y-m-d', strtotime($course['course_startDate'])); ?>">
                    </div>
                    <div class="flex flex-col gap-1 flex-1">
                        <label for="startTime">Start Time</label>
                        <input class="p-2 rounded-md border border-gray-300" type="time" name="course-start-time" id="startTime" value="<?php echo date('H:i', strtotime($course['course_startDate'])); ?>">
                    </div>
                </div>

                <div class="flex gap-1">
                    <div class="flex flex-col gap-1 flex-1">
                        <label for="endDate">End Date</label>
                        <input class="p-2 rounded-md border border-gray-300" type="date" name="course-end-date" id="endDate" value="<?php echo date('Y-m-d', strtotime($course['course_endDate'])); ?>">
                    </div>
                    <div class="flex flex-col gap-1 flex-1">
                        <label for="endTime">End Time</label>
                        <input class="p-2 rounded-md border border-gray-300" type="time" name="course-end-time" id="endTime" value="<?php echo date('H:i', strtotime($course['course_endDate'])); ?>">
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <label for="status">Status</label>
                    <select name="course-status" id="status" class="p-2 rounded-md border border-gray-300">
                        <option value="Active" <?php echo $course['course_status'] == 1 ? 'selected' : ''; ?>>Active</option>
                        <option value="Inactive" <?php echo $course['course_status'] == 0 ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>

                <button type="submit" class="bg-blue-500 text-white p-2 rounded-md">Update Course</button>
            </form>
        </div>
    </div>
</body>
</html>
