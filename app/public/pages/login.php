<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new SQLite3('../users.db');
    if (!$db) {
        error_log("Failed to connect to the database: " . $db->lastErrorMsg());
        echo "Failed to connect to the database.";
        exit();
    }

    $email = $_POST['email'];
    $password = $_POST['password'];
    $stmt = $db->prepare('SELECT * FROM users WHERE email = :email');
    if (!$stmt) {
        error_log("Failed to prepare statement: " . $db->lastErrorMsg());
        echo "Failed to prepare statement.";
        exit();
    }

    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $result = $stmt->execute();
    if (!$result) {
        error_log("Failed to execute statement: " . $db->lastErrorMsg());
        echo "Failed to execute statement.";
        exit();
    }

    $user = $result->fetchArray(SQLITE3_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php');
        exit();
    } else {
        error_log("Login failed for email: " . $email);
        echo "Login failed!";
    }
}
?>
<main class="login-container">
    <img class="go-back" src="public/resources/logo.png" alt="logo" onclick="window.location.href = '/'">
    <section class="login-box">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
            <button type="submit" class="contrast">Log In</button>
        </form>
    </section>
</main>
