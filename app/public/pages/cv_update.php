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

if (!isset($_GET['id'])) {
    exit('No CV ID provided');
}

$cv_id = $_GET['id'];

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

    $stmt = $db->prepare('UPDATE cv_data SET title = :title, description = :description, name = :name, email = :email, phone = :phone, address = :address, education = :education, experience = :experience, skills = :skills WHERE id = :id AND user_id = :user_id');
    if (!$stmt) {
        exit('SQL prepare statement failed: ' . $db->lastErrorMsg());
    }
    $stmt->bindValue(':title', $title, SQLITE3_TEXT);
    $stmt->bindValue(':description', $description, SQLITE3_TEXT);
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $stmt->bindValue(':phone', $phone, SQLITE3_TEXT);
    $stmt->bindValue(':address', $address, SQLITE3_TEXT);
    $stmt->bindValue(':education', $education, SQLITE3_TEXT);
    $stmt->bindValue(':experience', $experience, SQLITE3_TEXT);
    $stmt->bindValue(':skills', $skills, SQLITE3_TEXT);
    $stmt->bindValue(':id', $cv_id, SQLITE3_INTEGER);
    $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
    $result = $stmt->execute();

    if (!$result) {
        exit('SQL execution failed: ' . $db->lastErrorMsg());
    }

    header('Location: /?page=dashboard');
    exit();
}

// Fetch CV data for editing
$query = $db->prepare('SELECT title, description, name, email, phone, address, education, experience, skills FROM cv_data WHERE id = :id AND user_id = :user_id');
$query->bindValue(':id', $cv_id, SQLITE3_INTEGER);
$query->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
$result = $query->execute();
$cv = $result->fetchArray(SQLITE3_ASSOC);

if (!$cv) {
    exit('CV not found');
}
?>
<main class="cv-container">
    <h2>Edit CV</h2>
    <form action="/?page=cv_update&id=<?php echo $cv_id; ?>" method="POST">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($cv['title']); ?>" required>
        
        <label for="description">Description</label>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($cv['description']); ?></textarea>
        
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($cv['name']); ?>" required>
        
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($cv['email']); ?>" >
        
        <label for="phone">Phone</label>
        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($cv['phone']); ?>" >
        
        <label for="address">Address</label>
        <textarea id="address" name="address" ><?php echo htmlspecialchars($cv['address']); ?></textarea>
        
        <label for="education">Education</label>
        <textarea id="education" name="education" ><?php echo htmlspecialchars($cv['education']); ?></textarea>
        
        <label for="experience">Experience</label>
        <textarea id="experience" name="experience" ><?php echo htmlspecialchars($cv['experience']); ?></textarea>
        
        <label for="skills">Skills</label>
        <textarea id="skills" name="skills" ><?php echo htmlspecialchars($cv['skills']); ?></textarea>
        
        <input type="submit" value="Update CV">
    </form>
</main>