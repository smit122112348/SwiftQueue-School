<?php
    session_start();
    if (!isset($_SESSION['user'])) {
        header("Location: /login");
        exit();
    }
    require_once 'db.php';
    require_once 'models/Course.php';

    $conn = require 'db.php'; // Get the database connection

    if ($conn) {
        $coursesObj = new Course($conn);
        $stmt = $coursesObj->getAllCourses();
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Swiftqueue School</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function handleDelete(event, courseId, courseName) {
            event.preventDefault();
            // confirm before deleting
            if (confirm(`Are you sure you want to delete ${courseName}?`)) {
                fetch('/deleteCourse', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
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

        function handleEdit(event, courseId) {
            event.preventDefault();
            window.location.href = `/editCourse?courseId=${courseId}`;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const logoutButton = document.getElementById('logout-btn');
            const newCourseButton = document.getElementById('new-course-btn');
            logoutButton.addEventListener('click', function(e) {
                e.preventDefault();
                fetch('/logout', {
                    method: 'POST'
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
            });

            newCourseButton.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = '/newCourse';
            });
        });

    </script>
</head>
<body>
    <div class="container mx-auto flex flex-col justify-center items-center gap-5">
        <h1 class="text-4xl font-bold text-center mt-10">Swiftqueue School</h1>
        <?php 
            if ($conn) {
                if($stmt->rowCount() > 0) {
                    echo "<div class='w-2/3 p-5 bg-slate-200 rounded-md shadow-lg'>
                            <div class='flex justify-end'>
                                <button id='logout-btn' class='bg-red-500 text-white p-2 rounded-md shadow-md'>Logout</button>
                            </div>    
                            <div class='flex justify-between items-center'>
                                <h2 class='text-2xl font-bold my-5'>Courses:</h2>
                                <button id='new-course-btn' class='bg-green-500 text-white p-2 rounded-md h-fit shadow-md'>Add New Course</button>
                            </div>
                            <div class='flex flex-col items-center justify-center gap-5'>";
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<div class='w-full bg-slate-300 rounded-md p-5 shadow-md flex'>" . 
                                "<div class='flex flex-col flex-1'>".
                                "<h3 class='text-xl font-bold'>" . htmlspecialchars($row['course_name']) . "</h3>" .
                                "<p>" . htmlspecialchars($row['course_description']) . "</p>" .
                                "<p>Start Date: " . htmlspecialchars($row['course_startDate']) . "</p>" .
                                "<p>End Date: " . htmlspecialchars($row['course_endDate']) . "</p>" .
                                "</div>".
                                "<div class='flex flex-1 justify-between'>" .
                                "<p class='font-bold " . ($row['course_status'] === "Active" ? "text-green-500" : "text-red-500") . "'>" . htmlspecialchars($row['course_status']) . "</p>".
                                "<div class='flex flex-col gap-1 items-end'>".
                                    "<button class='bg-yellow-500 text-white p-2 rounded-md w-fit shadow-md' onclick='handleEdit(event, " . $row['course_id'] . " )'>Edit</button>" .
                                    "<button class='bg-red-500 text-white p-2 rounded-md mt-2 w-fit shadow-md' onclick='handleDelete(event, " . $row['course_id'] . ", \"" . addslashes($row['course_name']) . "\")'>Delete</button>" .
                                "</div>".
                                "</div>".
                            "</div>";
                    }
                    echo "</div></div>";
                } else {
                    echo "<p class='text-center mt-10'>No courses available</p>";
                }
            } else {
                echo "<p class='text-center mt-10'>Database connection failed.</p>";
            }
        ?>
    </div>
</body>
</html>
