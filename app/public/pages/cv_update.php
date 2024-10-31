<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    exit('User not logged in');
}

if (!isset($_SESSION['cv_id'])) {
    exit('No CV ID provided');
}

$cv_id = $_SESSION['cv_id'];
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
    $experience = $_POST['experience'] ?? '';

    // Handle skills as a JSON object
    $skills = [];
    if (isset($_POST['skill_title']) && isset($_POST['skill_description']) && isset($_POST['years_of_experience'])) {
        for ($i = 0; $i < count($_POST['skill_title']); $i++) {
            $title = $_POST['skill_title'][$i];
            $description = $_POST['skill_description'][$i];
            $years_of_experience = $_POST['years_of_experience'][$i];

            // Only add non-empty skills
            if ($title !== '' || $description !== '' || $years_of_experience !== '') {
                $skills[] = [
                    'title' => $title,
                    'description' => $description,
                    'years_of_experience' => $years_of_experience
                ];
            }
        }
    }
    $skills_json = json_encode($skills);

    // Handle education as a JSON object
    $education_entries = [];
    if (isset($_POST['education_institution']) && isset($_POST['education_degree']) && isset($_POST['education_years'])) {
        for ($i = 0; $i < count($_POST['education_institution']); $i++) {
            $institution = $_POST['education_institution'][$i];
            $degree = $_POST['education_degree'][$i];
            $years = $_POST['education_years'][$i];

            // Only add non-empty education entries
            if ($institution !== '' || $degree !== '' || $years !== '') {
                $education_entries[] = [
                    'institution' => $institution,
                    'degree' => $degree,
                    'years' => $years
                ];
            }
        }
    }
    $education_json = json_encode($education_entries);

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
    $stmt->bindValue(':education', $education_json, SQLITE3_TEXT);
    $stmt->bindValue(':experience', $experience, SQLITE3_TEXT);
    $stmt->bindValue(':skills', $skills_json, SQLITE3_TEXT);
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

// Decode the skills and education JSON
$skills = json_decode($cv['skills'], true);
$education = json_decode($cv['education'], true);
?>
<main class="container">
    <h2>Edit CV</h2>
    <form action="/?page=cv_update" method="POST" onsubmit="removeEmptySkills(); removeEmptyEducation();">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($cv['title']); ?>" required>
        
        <label for="description">Description</label>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($cv['description']); ?></textarea>
        
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($cv['name']); ?>" required>
        
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($cv['email']); ?>">
        
        <label for="phone">Phone</label>
        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($cv['phone']); ?>">
        
        <label for="address">Address</label>
        <textarea id="address" name="address"><?php echo htmlspecialchars($cv['address']); ?></textarea>
        
        <div id="education-container">
            <h3>Education</h3>
            <?php if (!empty($education)): ?>
                <?php foreach ($education as $entry): ?>
                    <div class="education-entry">
                        <label for="education_institution">School</label>
                        <input type="text" name="education_institution[]" value="<?php echo htmlspecialchars($entry['institution']); ?>">
                        
                        <label for="education_degree">Start date</label>
                        <input type="date" name="education_degree[]" value="<?php echo htmlspecialchars($entry['degree']); ?>">
                        
                        <label for="education_years">End date</label>
                        <input type="date" name="education_years[]" value="<?php echo htmlspecialchars($entry['years']); ?>">
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="education-entry">
                    <label for="education_institution">School</label>
                    <input type="text" name="education_institution[]">
                    
                    <label for="education_degree">Start date</label>
                    <input type="date" name="education_degree[]">
                    
                    <label for="education_years">End date</label>
                    <input type="date" name="education_years[]">
                </div>
            <?php endif; ?>
        </div>
        <button type="button" onclick="addEducation()">Add Another Education</button>
        
        <label for="experience">Experience</label>
        <textarea id="experience" name="experience"><?php echo htmlspecialchars($cv['experience']); ?></textarea>
        
        <div id="skills-container">
            <h3>Skills</h3>
            <?php if (!empty($skills)): ?>
                <?php foreach ($skills as $skill): ?>
                    <div class="skill-entry">
                        <label for="skill_title">Skill Title</label>
                        <input type="text" name="skill_title[]" value="<?php echo htmlspecialchars($skill['title']); ?>">
                        
                        <label for="skill_description">Skill Description</label>
                        <textarea name="skill_description[]"><?php echo htmlspecialchars($skill['description']); ?></textarea>
                        
                        <label for="years_of_experience">Years of Experience</label>
                        <input type="number" name="years_of_experience[]" value="<?php echo htmlspecialchars($skill['years_of_experience']); ?>">
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="skill-entry">
                    <label for="skill_title">Skill Title</label>
                    <input type="text" name="skill_title[]">
                    
                    <label for="skill_description">Skill Description</label>
                    <textarea name="skill_description[]"></textarea>
                    
                    <label for="years_of_experience">Years of Experience</label>
                    <input type="number" name="years_of_experience[]">
                </div>
            <?php endif; ?>
        </div>
        <button type="button" onclick="addSkill()">Add Another Skill</button>
        
        <input type="submit" value="Update CV">
    </form>
</main>
<link rel="stylesheet" href="styles.css">
<script src="scripts.js"></script>