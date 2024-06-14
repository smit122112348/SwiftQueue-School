<!DOCTYPE html>
<html>
<head>
    <title>Swiftqueue School</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const logoutButton = document.getElementById('logout-btn');
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
        });
    </script>
</head>
<body>
    <div class="container mx-auto flex flex-col justify-center items-center gap-5">
        <h1 class="text-4xl font-bold text-center mt-10">Swiftqueue School</h1>
        <div class="w-2/3 p-5 bg-slate-200 rounded-md shadow-lg">
            <div class="flex justify-end">
                <button id="logout-btn" class="bg-red-500 text-white p-2 rounded-md">Logout</button>
            </div>
            <h2 class="text-2xl font-bold my-5">Add New Course</h2>
            <form action="/newCourse" method="POST" class="flex flex-col gap-5">

                <div class="flex flex-col gap-1">
                <label for="course-name">Course Name:</label>
                <input type="text" id="course-name" name="course-name" class="p-2 rounded-md border border-gray-300" required>
                </div>

                <div class="flex flex-col gap-1">
                <label for="course-description">Course Description:</label>
                <textarea id="course-description" name="course-description" class="p-2 rounded-md border border-gray-300"></textarea>
                </div>

                <div class="flex gap-1">
                <div class="flex flex-col gap-1 flex-1">
                <label for="course-start-date">Start Date:</label>
                <input type="date" id="course-start-date" name="course-start-date" class="p-2 rounded-md border border-gray-300" required>
                </div>
                <div class="flex flex-col gap-1 flex-1">
                <label for="course-start-time">Start Time:</label>    
                <input type="time" id="course-start-time" name="course-start-time" class="p-2 rounded-md border border-gray-300" required>
                </div>
                </div>

                <div class="flex gap-1">
                <div class="flex flex-col gap-1 flex-1">
                <label for="course-end-date">End Date:</label>
                <input type="date" id="course-end-date" name="course-end-date" class="p-2 rounded-md border border-gray-300" required>
                </div>
                <div class="flex flex-col gap-1 flex-1">
                <label for="course-end-time">End Time:</label>
                <input type="time" id="course-end-time" name="course-end-time" class="p-2 rounded-md border border-gray-300" required>
                </div>
                </div>

                <div class="flex flex-col gap-1">
                <label for="course-status">Status:</label>
                <select id="course-status" name="course-status" class="p-2 rounded-md border border-gray-300" required>
                    <option value="Inactive">Inactive</option>  
                    <option value="Active">Active</option>
                </select>
                </div>

                <button type="submit" class="bg-green-500 text-white p-2 rounded-md">Add Course</button>
            </form>
        </div>
    </div>
</body>
</html>