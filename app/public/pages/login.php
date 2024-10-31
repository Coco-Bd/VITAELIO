<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$login = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new SQLite3(__DIR__ . '/../../users.db');
    $email = $_POST['email'];
    $password = $_POST['password'];
    $stmt = $db->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $result = $stmt->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $login = '';
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        header("Location: /?page=dashboard");
        exit();
    } else {
        $login = "Invalid email or password";
    }
}
?>
<main class="login-container">
    <img class="go-back" src="public/resources/logo.png" alt="logo" onclick="window.location.href = '/'">
    <section class="login-box">
        <h2>Login</h2>
        <form action="/?page=login" method="POST">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
            <p class="error"><?php echo $login; ?></p>
            <button type="submit">Log In</button>
        </form>
    </section>
</main>