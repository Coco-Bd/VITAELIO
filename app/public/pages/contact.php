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
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';

    // Update first name and last name if provided
    if (!empty($first_name) && !empty($last_name)) {
        $stmt = $db->prepare('UPDATE users SET user_firstName = :first_name, user_lastName = :last_name WHERE id = :user_id');
        $stmt->bindValue(':first_name', $first_name, SQLITE3_TEXT);
        $stmt->bindValue(':last_name', $last_name, SQLITE3_TEXT);
        $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
        $stmt->execute();

        // Update session variables
        $_SESSION['user_firstName'] = $first_name;
        $_SESSION['user_lastName'] = $last_name;
    }

    header('Location: /?page=dashboard'); // Redirect to the dashboard or index page
    exit();
}

$query = $db->prepare('SELECT user_firstName, user_lastName, email FROM users WHERE id = :user_id');
$query->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
$result = $query->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);

if (!$user) {
    exit('User not found');
}

$first_name = !empty($user['user_firstName']) ? htmlspecialchars($user['user_firstName']) : '';
$last_name = !empty($user['user_lastName']) ? htmlspecialchars($user['user_lastName']) : '';
$email = htmlspecialchars($user['email']);
?>
<main class="container">
    <h2>Contact Us</h2>
    <form action="/?page=contact" method="POST">
        <label for="first_name">First Name</label>
        <input type="text" id="first_name" name="first_name" value="<?php echo $first_name; ?>" <?php echo $first_name ? 'readonly' : 'required'; ?>>
        
        <label for="last_name">Last Name</label>
        <input type="text" id="last_name" name="last_name" value="<?php echo $last_name; ?>" <?php echo $last_name ? 'readonly' : 'required'; ?>>
        
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo $email; ?>" readonly>
        
        <label for="message">Message</label>
        <textarea id="message" name="message" required></textarea>
        
        <input type="submit" value="Send Message">
    </form>
</main>