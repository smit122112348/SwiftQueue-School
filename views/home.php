<?php
// This is the home page
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /login");
    exit();
}
require_once 'models/Course.php';

$conn = require 'db.php'; // Get the database connection

if ($conn) {
    $coursesObj = new Course($conn);
    $stmt = $coursesObj->getAllCourses();
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

        // Function to handle course deletion
        function handleDelete(event, courseId, courseName) {
            event.preventDefault();
            
            // confirm before deleting
            if (confirm(`Are you sure you want to delete ${courseName}?`)) {
                
                // Make a DELETE request to delete the course
                fetch('/deleteCourse', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?php echo $csrf_token ?>' // Add CSRF token to headers

                    },
                    body: JSON.stringify({ courseId: courseId })
                })
                .then(response => {
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        alert('Delete failed');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
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

        // Function to handle filter
        function handleFilter(event){
            // If filter is "Showing All Courses", the change to "Showing Active Courses"
            if(event.target.innerText === "Showing All Courses"){
                event.target.innerText = "Showing Active Courses";
                let courses = document.querySelectorAll('.course');
                courses.forEach(function(c){
                    if(c.querySelector('.active') === null){
                        c.style.display = "none";
                    }
                    else{
                        c.style.display = "flex";
                    }
                });
                
            } 
            // If filter is "Showing Active Courses", the change to "Showing Inactive Courses"
            else if(event.target.innerText === "Showing Active Courses"){
                event.target.innerText = "Showing Inactive Courses";
                let courses = document.querySelectorAll('.course');
                courses.forEach(function(c){
                    if(c.querySelector('.inactive') === null){
                        c.style.display = "none";
                    }
                    else{
                        c.style.display = "flex";
                    }
                });
            }

            // If filter is "Showing Inactive Courses", the change to "Showing All Courses"
            else if(event.target.innerText === "Showing Inactive Courses"){
                event.target.innerText = "Showing All Courses";
                let courses = document.querySelectorAll('.course');
                courses.forEach(function(c){
                    c.style.display = "flex";
                });
            }
        }


    </script>
</head>
<body>
    <div class="container mx-auto flex flex-col justify-center items-center gap-5">
        <h1 class="text-4xl font-bold text-center mt-10">Swiftqueue School</h1>

        <?php if ($conn): ?>

            <!-- Check the row count -->
            <?php if ($stmt->rowCount() > 0): ?>

                <div class='w-2/3 p-5 bg-slate-200 rounded-md shadow-lg'>
                    <div class='flex gap-5 justify-between'>
                        <a href='/userDetails' class='bg-blue-500 text-white p-2 rounded-md shadow-md'>Welcome, <?= $_SESSION['user']['user_full_name'] ?></a>
                        <button id='logout-btn' class='bg-red-500 text-white p-2 rounded-md shadow-md' onclick="handleLogout(event)">Logout</button>
                    </div>
                    <div class='flex justify-between items-center'>
                        <h2 class='text-2xl font-bold my-5'>Courses:</h2>
                        <div class='flex gap-5'>
                            <button id='filter-btn' class='bg-blue-500 text-white p-2 rounded-md shadow-md' onclick='handleFilter(event)'>Showing All Courses</button>
                            <a href='/newCourse' class='bg-green-500 text-white p-2 rounded-md h-fit shadow-md'>Add New Course</a>
                        </div>
                    </div>
                    <div class='flex flex-col items-center justify-center gap-5'>

                        <!-- Loop to display all courses -->
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>

                            <div class='w-full bg-slate-300 rounded-md p-5 shadow-md flex course'>
                                <div class='flex flex-col flex-1'>
                                    <h3 class='text-xl font-bold'><?= htmlspecialchars($row['course_name']) ?></h3>
                                    <p><?= htmlspecialchars($row['course_description']) ?></p>
                                    <p>Start Date: <?= htmlspecialchars($row['course_startDate']) ?></p>
                                    <p>End Date: <?= htmlspecialchars($row['course_endDate']) ?></p>
                                </div>
                                <div class='flex flex-1 justify-between'>
                                    <p class='font-bold <?= $row['course_status'] === "Active" ? "text-green-500 active" : "text-red-500 inactive" ?>'><?= htmlspecialchars($row['course_status']) ?></p>
                                    <div class='flex flex-col gap-1 items-end'>
                                        <a href='/editCourse?courseId=<?= $row['course_id'] ?>' class='bg-yellow-500 text-white p-2 rounded-md w-fit shadow-md'>Edit</a>
                                        <button class='bg-red-500 text-white p-2 rounded-md mt-2 w-fit shadow-md' onclick='handleDelete(event, <?= $row['course_id'] ?>, "<?= addslashes($row['course_name']) ?>")'>Delete</button>
                                    </div>
                                </div>
                            </div>

                        <?php endwhile; ?>

                    </div>
                </div>

            <?php else: ?>

                <div class='flex gap-5 justify-between'>
                    <button id='user-detail-btn' class='bg-blue-500 text-white p-2 rounded-md shadow-md'>Welcome, <?= $_SESSION['user']['user_full_name'] ?></button>
                    <button id='logout-btn' class='bg-red-500 text-white p-2 rounded-md shadow-md'>Logout</button>
                </div>
                <button id='new-course-btn' class='bg-green-500 text-white p-2 rounded-md h-fit shadow-md'>Add New Course</button>
                <p class='text-center mt-10'>No courses available</p>

            <?php endif; ?>

        <?php else: ?>

            <p class='text-center mt-10'>Database connection failed.</p>

        <?php endif; ?>

    </div>
</body>
</html>
