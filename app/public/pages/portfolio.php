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
    $image = '';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploads_dir = __DIR__ . '/uploads/';
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0777, true);
        }
        $image = '/uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $uploads_dir . basename($_FILES['image']['name']));
    }

    // Handle technologies as a JSON object
    $tech_entries = [];
    if (isset($_POST['tech_name']) && isset($_POST['tech_description'])) {
        for ($i = 0; $i < count($_POST['tech_name']); $i++) {
            $name = $_POST['tech_name'][$i];
            $description = $_POST['tech_description'][$i];

            // Only add non-empty technology entries
            if ($name !== '' || $description !== '') {
                $tech_entries[] = [
                    'name' => $name,
                    'description' => $description
                ];
            }
        }
    }
    $technologies_json = json_encode($tech_entries);

    $stmt = $db->prepare('INSERT INTO projects (user_id, title, description, technologies, image) VALUES (:user_id, :title, :description, :technologies, :image)');
    if (!$stmt) {
        exit('SQL prepare statement failed: ' . $db->lastErrorMsg());
    }
    $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
    $stmt->bindValue(':title', $title, SQLITE3_TEXT);
    $stmt->bindValue(':description', $description, SQLITE3_TEXT);
    $stmt->bindValue(':technologies', $technologies_json, SQLITE3_TEXT);
    $stmt->bindValue(':image', $image, SQLITE3_TEXT);
    $result = $stmt->execute();

    if (!$result) {
        exit('SQL execution failed: ' . $db->lastErrorMsg());
    }

    header('Location: /?page=portfolio');
    exit();
}

// Fetch projects
$query = $db->prepare('SELECT * FROM projects WHERE user_id = :user_id');
$query->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
$result = $query->execute();
$projects = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $projects[] = $row;
}
?>
<main class="container">
    <?php if (empty($projects)): ?>
        <h2>Create New Project</h2>
        <form action="/?page=portfolio" method="POST" enctype="multipart/form-data" onsubmit="removeEmptyTechnologies()">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required>
            
            <label for="description">Description</label>
            <textarea id="description" name="description" required></textarea>
            
            <label for="image">Project Image</label>
            <input type="file" id="image" name="image" accept="image/*">
            
            <div id="technologies-container">
                <h3>Technologies</h3>
                <div class="tech-entry">
                    <label for="tech_name">Technology Name</label>
                    <input type="text" name="tech_name[]">
                    
                    <label for="tech_description">Technology Description</label>
                    <textarea name="tech_description[]"></textarea>
                </div>
            </div>
            <button type="button" onclick="addTechnology()">Add Another Technology</button>
            
            <input type="submit" value="Create Project">
        </form>
    <?php else: ?>
        <h2>Your Projects</h2>
        <ul>
            <?php foreach ($projects as $project): ?>
                <li>
                    <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                    <p><?php echo htmlspecialchars($project['description']); ?></p>
                    <?php if (!empty($project['image'])): ?>
                        <img src="<?php echo htmlspecialchars($project['image']); ?>" alt="Project Image" style="max-width: 200px;">
                    <?php endif; ?>
                    <p>Technologies:</p>
                    <ul>
                        <?php
                        $technologies = json_decode($project['technologies'], true);
                        if (!empty($technologies)) {
                            foreach ($technologies as $tech) {
                                echo '<li>' . htmlspecialchars($tech['name']) . ': ' . htmlspecialchars($tech['description']) . '</li>';
                            }
                        }
                        ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>
        <button type="button" onclick="showProjectForm()">Create New Project</button>
        <div id="project-form" style="display:none;">
            <h2>Create New Project</h2>
            <form action="/?page=portfolio" method="POST" enctype="multipart/form-data" onsubmit="removeEmptyTechnologies()">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required>
                
                <label for="description">Description</label>
                <textarea id="description" name="description" required></textarea>
                
                <label for="image">Project Image</label>
                <input type="file" id="image" name="image" accept="image/*">
                
                <div id="technologies-container">
                    <h3>Technologies</h3>
                    <div class="tech-entry">
                        <label for="tech_name">Technology Name</label>
                        <input type="text" name="tech_name[]">
                        
                        <label for="tech_description">Technology Description</label>
                        <textarea name="tech_description[]"></textarea>
                    </div>
                </div>
                <button type="button" onclick="addTechnology()">Add Another Technology</button>
                
                <input type="submit" value="Create Project">
            </form>
        </div>
    <?php endif; ?>
</main>
<link rel="stylesheet" href="styles.css">
<script src="scripts.js"></script>