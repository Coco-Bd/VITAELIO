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
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $admin_password = $_POST['admin_password'] ?? '';

    // Update user information
    $stmt = $db->prepare('UPDATE users SET user_firstName = :first_name, user_lastName = :last_name, email = :email WHERE id = :user_id');
    $stmt->bindValue(':first_name', $first_name, SQLITE3_TEXT);
    $stmt->bindValue(':last_name', $last_name, SQLITE3_TEXT);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
    $stmt->execute();

    // Update password if provided and matches confirmation
    if (!empty($password)) {
        if ($password === $confirm_password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare('UPDATE users SET password = :password WHERE id = :user_id');
            $stmt->bindValue(':password', $hashed_password, SQLITE3_TEXT);
            $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
            $stmt->execute();
            $_SESSION['message'] = 'Password updated successfully';
        } else {
            $_SESSION['message'] = 'Passwords do not match';
        }
    } else {
        $_SESSION['message'] = 'Profile updated successfully';
    }

    // Check admin password and update role if correct
    $correct_admin_password = 'admin123'; // Example password, replace with your actual admin password
    if ($admin_password === $correct_admin_password) {
        $stmt = $db->prepare('UPDATE users SET role = "admin" WHERE id = :user_id');
        $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
        $stmt->execute();
        $_SESSION['role'] = 'admin';
        $_SESSION['message'] = 'Admin role granted';
    }

    header('Location: /?page=profile');
    exit();
}

// Fetch user information
$query = $db->prepare('SELECT user_firstName, user_lastName, email FROM users WHERE id = :user_id');
$query->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
$result = $query->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);

if (!$user) {
    exit('User not found');
}

$first_name = htmlspecialchars($user['user_firstName']);
$last_name = htmlspecialchars($user['user_lastName']);
$email = htmlspecialchars($user['email']);
?>
<main class="container">
    <h2>Update Profile</h2>
    <?php if (isset($_SESSION['message'])): ?>
        <p><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></p>
    <?php endif; ?>
    <form action="/?page=profile" method="POST">
        <label for="first_name">First Name</label>
        <input type="text" id="first_name" name="first_name" value="<?php echo $first_name; ?>" required>
        
        <label for="last_name">Last Name</label>
        <input type="text" id="last_name" name="last_name" value="<?php echo $last_name; ?>" required>
        
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
        
        <label for="password">New Password</label>
        <input type="password" id="password" name="password">
        
        <label for="confirm_password">Confirm New Password</label>
        <input type="password" id="confirm_password" name="confirm_password">
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <p>Admin role granted</p>
        <?php else: ?>
        <label for="admin_password">Admin Password</label>
        <input type="password" id="admin_password" name="admin_password">
        <?php endif; ?>
        <input type="submit" value="Update Profile">
    </form>
</main>