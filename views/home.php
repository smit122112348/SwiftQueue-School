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
        document.addEventListener('DOMContentLoaded', function() {
            const logoutButton = document.getElementById('logout-btn');
            const newCourseButton = document.getElementById('new-course-btn');
            logoutButton.addEventListener('click', function() {
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

            newCourseButton.addEventListener('click', function() {
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
                                <button id='logout-btn' class='bg-red-500 text-white p-2 rounded-md'>Logout</a>
                            </div>    
                            <div class='flex justify-between items-center'>
                                <h2 class='text-2xl font-bold my-5'>Courses:</h2>
                                <button id='new-course-btn' class='bg-green-500 text-white p-2 rounded-md h-fit'>Add New Course</a>
                            </div>
                            <div class='flex flex-col items-center justify-center gap-5'>";
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<div class='w-full bg-slate-300 rounded-md p-5 shadow-md'>" . 
                                "<h3 class='text-xl font-bold'>" . htmlspecialchars($row['course_name']) . "</h3>" .
                                "<p>" . htmlspecialchars($row['course_description']) . "</p>" .
                                "<p>Start Date: " . htmlspecialchars($row['course_startDate']) . "</p>" .
                                "<p>End Date: " . htmlspecialchars($row['course_endDate']) . "</p>" .
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
