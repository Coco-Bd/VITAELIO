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
    $stmt->bindValue(':education', $education_json, SQLITE3_TEXT);
    $stmt->bindValue(':experience', $experience, SQLITE3_TEXT);
    $stmt->bindValue(':skills', $skills_json, SQLITE3_TEXT);
    $result = $stmt->execute();

    if (!$result) {
        exit('SQL execution failed: ' . $db->lastErrorMsg());
    }

    // Debugging: Log successful insertion
    error_log("CV data inserted successfully");

    header('Location: /?page=dashboard');
    exit();
}

// Fetch user information
$query = $db->prepare('SELECT user_firstName, user_lastName FROM users WHERE id = :user_id');
$query->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
$result = $query->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);

if (!$user) {
    exit('User not found');
}

$first_name = !empty($user['user_firstName']) ? htmlspecialchars($user['user_firstName']) : '';
$last_name = !empty($user['user_lastName']) ? htmlspecialchars($user['user_lastName']) : '';
$name = trim($first_name . ' ' . $last_name);
?>
<main class="container">
    <h2>Create New CV</h2>
    <form action="/?page=create_cv" method="POST" onsubmit="removeEmptySkills(); removeEmptyEducation();">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" required>
        
        <label for="description">Description</label>
        <textarea id="description" name="description" required></textarea>
        
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?php echo $name; ?>">
        
        <label for="email">Email</label>
        <input type="email" id="email" name="email">
        
        <label for="phone">Phone</label>
        <input type="text" id="phone" name="phone">
        
        <label for="address">Address</label>
        <textarea id="address" name="address"></textarea>
        
        
        <div id="education-container">
            <h3>Education</h3>
            <div class="education-entry">
                <label for="education_institution">School</label>
                <input type="text" name="education_institution[]">
                
                <label for="education_degree">Start date</label>
                <input type="date" name="education_degree[]">
                
                <label for="education_years">End date</label>
                <input type="date" name="education_years[]">
            </div>
        </div>
        <button type="button" onclick="addEducation()">Add Another Education</button>
        
        <label for="experience">Experience</label>
        <textarea id="experience" name="experience"></textarea>
        
        <div id="skills-container">
            <h3>Skills</h3>
            <div class="skill-entry">
                <label for="skill_title">Skill Title</label>
                <input type="text" name="skill_title[]">
                
                <label for="skill_description">Skill Description</label>
                <textarea name="skill_description[]"></textarea>
                
                <label for="years_of_experience">Years of Experience</label>
                <input type="number" name="years_of_experience[]">
            </div>
        </div>
        <button type="button" onclick="addSkill()">Add Another Skill</button>
        
        <input type="submit" value="Create CV">
    </form>
</main>

<script>
function addSkill() {
    const container = document.getElementById('skills-container');
    const skillEntry = document.createElement('div');
    skillEntry.classList.add('skill-entry');
    skillEntry.innerHTML = `
        <label for="skill_title">Skill Title</label>
        <input type="text" name="skill_title[]">
        
        <label for="skill_description">Skill Description</label>
        <textarea name="skill_description[]"></textarea>
        
        <label for="years_of_experience">Years of Experience</label>
        <input type="number" name="years_of_experience[]">
    `;
    container.appendChild(skillEntry);
}

function removeEmptySkills() {
    const container = document.getElementById('skills-container');
    const skillEntries = container.getElementsByClassName('skill-entry');
    for (let i = skillEntries.length - 1; i >= 0; i--) {
        const skillEntry = skillEntries[i];
        const title = skillEntry.querySelector('input[name="skill_title[]"]').value.trim();
        const description = skillEntry.querySelector('textarea[name="skill_description[]"]').value.trim();
        const yearsOfExperience = skillEntry.querySelector('input[name="years_of_experience[]"]').value.trim();
        
        if (title === '' && description === '' && yearsOfExperience === '') {
            container.removeChild(skillEntry);
        }
    }
}

function addEducation() {
    const container = document.getElementById('education-container');
    const educationEntry = document.createElement('div');
    educationEntry.classList.add('education-entry');
    educationEntry.innerHTML = `
        <label for="education_institution">Institution</label>
        <input type="text" name="education_institution[]">
        
        <label for="education_degree">Degree</label>
        <input type="text" name="education_degree[]">
        
        <label for="education_years">Years</label>
        <input type="text" name="education_years[]">
    `;
    container.appendChild(educationEntry);
}

function removeEmptyEducation() {
    const container = document.getElementById('education-container');
    const educationEntries = container.getElementsByClassName('education-entry');
    for (let i = educationEntries.length - 1; i >= 0; i--) {
        const educationEntry = educationEntries[i];
        const institution = educationEntry.querySelector('input[name="education_institution[]"]').value.trim();
        const degree = educationEntry.querySelector('input[name="education_degree[]"]').value.trim();
        const years = educationEntry.querySelector('input[name="education_years[]"]').value.trim();
        
        if (institution === '' && degree === '' && years === '') {
            container.removeChild(educationEntry);
        }
    }
}
</script>
<style>
    main.container {
    max-width: 800px;
    margin: auto;
    padding: 20px;
    background-color: var(--pico-muted); /* Softer background color */
    border-radius: var(--pico-border-radius);
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--pico-contrast-border); /* Added border */
}

    form {
        display: flex;
        flex-direction: column;
    }

    label {
        margin-top: 10px;
    }

    input[type="text"],
    input[type="email"],
    input[type="number"],
    input[type="date"],
    textarea {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid var(--pico-contrast-border);
        border-radius: var(--pico-border-radius);
    }
    input:focus {
        outline: none;
        border: 2px solid #333848;
  
    }

    button[type="button"] {
        margin-top: 20px;
        padding: 10px;
        color: var(--pico-primary-inverse);
        border: none;
        border-radius: var(--pico-border-radius);
        cursor: pointer;
    }


    input[type="submit"] {
        margin-top: 20px;
        padding: 10px;
        border: none;
        border-radius: var(--pico-border-radius);
        cursor: pointer;
    }


    .skill-entry {
        margin-top: 20px;
        padding: 10px;
        background-color: var(--pico-muted);
        border-radius: var(--pico-border-radius);
    }

    .skill-entry label {
        margin-top: 10px;
    }
</style>