<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dbPath = '../users.db';
    $db = new SQLite3($dbPath);

    if (!$db) {
        error_log("Failed to connect to the database: " . $db->lastErrorMsg());
        echo "Failed to connect to the database.";
        exit();
    }

    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($email) || empty($password) || empty($confirm_password)) {
        echo "All fields are required!";
    } elseif ($password !== $confirm_password) {
        echo "Passwords do not match!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $db->prepare('INSERT INTO users (email, password) VALUES (:email, :password)');
        
        if (!$stmt) {
            error_log("Failed to prepare statement: " . $db->lastErrorMsg());
            echo "Failed to prepare statement.";
            exit();
        }

        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->bindValue(':password', $hashed_password, SQLITE3_TEXT);

        $result = $stmt->execute();
        if ($result) {
            echo "Registration successful!";
        } else {
            error_log("Failed to execute statement: " . $db->lastErrorMsg());
            echo "Registration failed!";
        }

        $stmt->close();
    }

    $db->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css">
    <link href="https://fonts.cdnfonts.com/css/segoe-ui-4" rel="stylesheet">
    <link rel="stylesheet" href="/public/static/style.css">
</head>
<body>
    <main class="login-container">
        <img class="go-back" src="/public/resources/logo.png" alt="logo" onclick="window.location.href = '/'">
        <section class="login-box">
            <h2>Register</h2>
            <form action="login.php" method="POST">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter your Email" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>

                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>

                <button type="submit" class="contrast">Register</button>
            </form>
        </section>
    </main>
</body>
</html>
