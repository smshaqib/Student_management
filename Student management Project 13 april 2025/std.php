
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Manager</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f9f9f9; }
        nav { background-color: #111; color: #fff; display: flex; justify-content: space-between; padding: 10px 20px; }
        nav .logo { font-weight: bold; }
        nav .links a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 600px; margin: 40px auto; background-color: #eee; padding: 30px; border-radius: 12px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-bottom: 20px; }
        label { display: block; margin-top: 15px; }
        input, select, textarea { width: 100%; padding: 10px; margin-top: 5px; border-radius: 5px; border: 1px solid #ccc; }
        button { margin-top: 20px; width: 100%; padding: 10px; background-color: #111; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }
        .table-container { max-width: 800px; margin: 40px auto; }
        table { width: 100%; border-collapse: collapse; background-color: #fff; }
        th, td { padding: 12px; border: 1px solid #ccc; text-align: center; }
        th { background-color: #444; color: #fff; }
        .message { text-align: center; margin-top: 20px; color: #888; }
        .error { text-align: center; margin-top: 20px; color: #d32f2f; }
    </style>
</head>
<body>
    <nav>
        <div class="logo">🎓 StudentManager</div>
        <div class="links">
            <a href="?page=add">Add Student</a>
            <a href="?page=list">Student List</a>
            <a href="?page=enroll">Enroll in Course</a>
            <a href="?page=enroll_history">Enrollment History</a>
        </div>
    </nav>

    <?php
    include 'db.php';
    $page = $_GET['page'] ?? 'add';
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $page === 'add') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $student_id = $_POST['student_id'];
        $department = $_POST['department'];
        $major = $_POST['major'];
        $dob = $_POST['dob'];
        $address = $_POST['address'];

        if (!empty($name) && !empty($email)) {
            $stmt = $conn->prepare("INSERT INTO students (name, email, student_id, department, major, dob, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $name, $email, $student_id, $department, $major, $dob, $address);
            if ($stmt->execute()) {
                echo "<p class='message'>Student registered successfully with ID: " . htmlspecialchars($student_id) . ".</p>";
            } else {
                echo "<p class='error'>Error registering student: " . $conn->error . ".</p>";
            }
        } else {
            echo "<p class='error'>Name and Email are required.</p>";
        }
    }

    if ($page === 'add') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['update_id'])) {
            $student_id = $_POST['student_id'];
    
            if (!empty($student_id)) {
                $check = $conn->prepare("SELECT id FROM students WHERE student_id = ?");
                $check->bind_param("s", $student_id);
                $check->execute();
                $check->store_result();
    
                if ($check->num_rows > 0) {
                    echo "<p class='error'>❌ Student ID already exists. Please use a unique Student ID.</p>";
                } else {
                    $stmt = $conn->prepare("INSERT INTO students (name, email, student_id, department, major, dob, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssssss", $_POST['name'], $_POST['email'], $_POST['student_id'], $_POST['department'], $_POST['major'], $_POST['dob'], $_POST['address']);
                    if ($stmt->execute()) {
                        echo "<p class='message'>✅ Student registered successfully.</p>";
                    } else {
                        echo "<p class='error'>❌ Error registering student: " . $conn->error . "</p>";
                    }
                }
            } else {
                // Allow insertion with empty student_id
                $stmt = $conn->prepare("INSERT INTO students (name, email, student_id, department, major, dob, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", $_POST['name'], $_POST['email'], $_POST['student_id'], $_POST['department'], $_POST['major'], $_POST['dob'], $_POST['address']);
                if ($stmt->execute()) {
                    echo "<p class='message'>✅ Student registered successfully.</p>";
                } else {
                    echo "<p class='error'>❌ Error registering student: " . $conn->error . "</p>";
                }
            }
        }
    ?>
        <div class="container">
            <h2>Register New Student</h2>
            <form method="POST">
                <label>Name*</label>
                <input name="name" type="text" required>
    
                <label>Email*</label>
                <input name="email" type="email" required>
    
                <label>Student ID</label>
                <input name="student_id" type="text">
    
                <label>Department</label>
                <input name="department" type="text">
    
                <label>Major</label>
                <input name="major" type="text">
    
                <label>Date of Birth</label>
                <input name="dob" type="date">
    
                <label>Address</label>
                <textarea name="address"></textarea>
    
                <button type="submit">Submit</button>
            </form>
        </div>
    <?php }
    
    
    
    elseif ($page === 'list') {
        $result = $conn->query("SELECT id, name, student_id, department, major, email FROM students");
    ?>
        <div class="table-container">
            <h2>Student List</h2>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Student ID</th>
                        <th>Department</th>
                        <th>Major</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>" . htmlspecialchars($row['name']) . "</td>
                                <td>" . htmlspecialchars($row['student_id']) . "</td>
                                <td>" . htmlspecialchars($row['department']) . "</td>
                                <td>" . htmlspecialchars($row['major']) . "</td>
                                <td>" . htmlspecialchars($row['email']) . "</td>
                                <td class='actions'>
                                    <a href='?page=add&edit_id=" . $row['id'] . "'>Edit</a>
                                    <a href='?page=list&delete_id=" . $row['id'] . "' onclick=\"return confirm('Are you sure you want to delete this student?');\">Delete</a>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No data in the table</td></tr>";
                    } ?>
                </tbody>
            </table>
        </div>
    <?php
    } elseif ($page === 'enroll') {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $student_id = $_POST['student_id'];
            $course_code = $_POST['course_code'];
            $course_title = $_POST['course_title'];
            $semester = $_POST['semester'];
            $grade = $_POST['grade'];

            if (!empty($student_id) && !empty($course_code)) {
                if (studentExists($conn, $student_id)) {
                    $stmt = $conn->prepare("INSERT INTO enrollments (student_id, course_code, course_title, semester, grade) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssss", $student_id, $course_code, $course_title, $semester, $grade);
                    if ($stmt->execute()) {
                        echo "<p class='message'>Course enrollment successful for student ID: " . htmlspecialchars($student_id) . ".</p>";
                    } else {
                        echo "<p class='error'>Error enrolling student: " . $conn->error . ".</p>";
                    }
                } else {
                    echo "<p class='error'>Student ID " . htmlspecialchars($student_id) . " does not exist.</p>";
                }
            } else {
                echo "<p class='error'>Student ID and Course Code are required.</p>";
            }


            
        }
    ?>
        <div class="container">
            <h2>Enroll in a Course</h2>
            <form method="POST">
                <label>Student ID*</label>
                <input name="student_id" type="text" required>

                <label>Course Code*</label>
                <input name="course_code" type="text" required>

                <label>Course Title</label>
                <input name="course_title" type="text">

                <label>Semester</label>
                <input name="semester" type="text">

                <label>Grade</label>
                <select name="grade">
                    <option value="">Select Grade</option>
                    <option value="A">A</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B">B</option>
                    <option value="B-">B-</option>
                    <option value="C+">C+</option>
                    <option value="C">C</option>
                    <option value="C-">C-</option>
                    <option value="D">D</option>
                    <option value="F">F</option>
                </select>

                <button type="submit">Enroll</button>
            </form>
        </div>
    <?php

        


    } elseif ($page === 'enroll_history') {
        $student_id = $_POST['student_id'] ?? '';
        $result = null;
        if (!empty($student_id)) {
            if (studentExists($conn, $student_id)) {
                $stmt = $conn->prepare("SELECT course_code, course_title, semester, grade FROM enrollments WHERE student_id = ?");
                $stmt->bind_param("s", $student_id);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                echo "<p class='error'>Student ID " . htmlspecialchars($student_id) . " does not exist.</p>";
            }
        }
    ?>
        <div class="container">
            <h2>Enrollment History</h2>
            <form method="POST">
                <label>Enter Student ID*</label>
                <input name="student_id" type="text" value="<?php echo htmlspecialchars($student_id); ?>" required>
                <button type="submit">Search</button>
            </form>
        </div>
        <?php if (!empty($student_id) && studentExists($conn, $student_id)) { ?>
        <div class="table-container">
            <h2>Student ID: <?php echo htmlspecialchars($student_id); ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Title</th>
                        <th>Semester</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $grade = $row['grade'] ?: '-'; // Display '-' if grade is empty
                        echo "<tr><td>" . htmlspecialchars($row['course_code']) . "</td><td>" . htmlspecialchars($row['course_title']) . "</td><td>" . htmlspecialchars($row['semester']) . "</td><td>" . htmlspecialchars($grade) . "</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No data available</td></tr>";
                } ?>
                </tbody>
            </table>
        </div>
        <?php } ?>
    <?php 

$result = $conn->query("SELECT name, student_id, department, major, email FROM students");
?>
    <div class="table-container">
        <h2>Student List</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Student ID</th>
                    <th>Department</th>
                    <th>Major</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr><td>" . htmlspecialchars($row['name']) . "</td><td>" . htmlspecialchars($row['student_id']) . "</td><td>" . htmlspecialchars($row['department']) . "</td><td>" . htmlspecialchars($row['major']) . "</td><td>" . htmlspecialchars($row['email']) . "</td></tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No data in the table</td></tr>";
            } ?>
            </tbody>
        </table>
    </div>
<?php

} ?>
</body>
</html>