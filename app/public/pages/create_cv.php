<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    exit('User not logged in');
}

$user_id = $_SESSION['user_id'];
$db = new SQLite3(__DIR__ . '/../../users.db');

if (!$db) {
    exit('Database connection failed: ' . $db->lastErrorMsg());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $education = $_POST['education'] ?? '';
    $experience = $_POST['experience'] ?? '';
    $skills = $_POST['skills'] ?? '';

    // Debugging: Log the received POST data
    error_log("Received POST data: " . print_r($_POST, true));

    $stmt = $db->prepare('INSERT INTO cv_data (user_id, title, description, name, email, phone, address, education, experience, skills) VALUES (:user_id, :title, :description, :name, :email, :phone, :address, :education, :experience, :skills)');
    if (!$stmt) {
        exit('SQL prepare statement failed: ' . $db->lastErrorMsg());
    }
    $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
    $stmt->bindValue(':title', $title, SQLITE3_TEXT);
    $stmt->bindValue(':description', $description, SQLITE3_TEXT);
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $stmt->bindValue(':phone', $phone, SQLITE3_TEXT);
    $stmt->bindValue(':address', $address, SQLITE3_TEXT);
    $stmt->bindValue(':education', $education, SQLITE3_TEXT);
    $stmt->bindValue(':experience', $experience, SQLITE3_TEXT);
    $stmt->bindValue(':skills', $skills, SQLITE3_TEXT);
    $result = $stmt->execute();

    if (!$result) {
        exit('SQL execution failed: ' . $db->lastErrorMsg());
    }

    // Debugging: Log successful insertion
    error_log("CV data inserted successfully");

    header('Location: /?page=dashboard');
    exit();
}
?>
<main class="cv-container">
    <h2>Create New CV</h2>
    <form action="/?page=create_cv" method="POST">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" required>
        
        <label for="description">Description</label>
        <textarea id="description" name="description" required></textarea>
        
        <label for="name">Name</label>
        <input type="text" id="name" name="name" required>
        
        <label for="email">Email</label>
        <input type="email" id="email" name="email">
        
        <label for="phone">Phone</label>
        <input type="text" id="phone" name="phone">
        
        <label for="address">Address</label>
        <textarea id="address" name="address"></textarea>
        
        <label for="education">Education</label>
        <textarea id="education" name="education"></textarea>
        
        <label for="experience">Experience</label>
        <textarea id="experience" name="experience"></textarea>
        
        <label for="skills">Skills</label>
        <textarea id="skills" name="skills"></textarea>
        
        <input type="submit" value="Create CV">
    </form>
</main>