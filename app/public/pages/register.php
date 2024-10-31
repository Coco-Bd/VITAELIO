<?php
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dbPath = realpath(__DIR__ . '/../../users.db');
    if ($dbPath === false) {
        error_log("Database file not found.");
        $error = "Database file not found.";
    } else {
        $db = new SQLite3($dbPath);

        if (!$db) {
            error_log("Failed to connect to the database: " . $db->lastErrorMsg());
            $error = "Failed to connect to the database.";
        } else {
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            if (empty($email) || empty($password) || empty($confirm_password)) {
                $error = "All fields are required!";
                error_log("All fields are required!");
            } elseif ($password !== $confirm_password) {
                $error = "Passwords do not match!";
                error_log("Passwords do not match!");
            } else {
                // Check if the email is already registered
                $stmt = $db->prepare('SELECT * FROM users WHERE email = :email');
                if (!$stmt) {
                    error_log("Failed to prepare SELECT statement: " . $db->lastErrorMsg());
                    $error = "Failed to prepare SELECT statement.";
                } else {
                    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
                    $result = $stmt->execute();
                    if ($result->fetchArray(SQLITE3_ASSOC)) {
                        $error = "Email is already registered!";
                        error_log("Email is already registered: " . $email);
                    } else {
                        // Insert the new user into the database
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $db->prepare('INSERT INTO users (email, password) VALUES (:email, :password)');
                        if (!$stmt) {
                            error_log("Failed to prepare INSERT statement: " . $db->lastErrorMsg());
                            $error = "Failed to prepare INSERT statement.";
                        } else {
                            $stmt->bindValue(':email', $email, SQLITE3_TEXT);
                            $stmt->bindValue(':password', $hashed_password, SQLITE3_TEXT);
                            $result = $stmt->execute();

                            if ($result) {
                                error_log("Registration successful for email: " . $email);
                                header('Location: /?page=login');
                                exit();
                            } else {
                                error_log("Failed to execute INSERT statement: " . $db->lastErrorMsg());
                                $error = "Failed to execute INSERT statement.";
                            }
                        }
                    }
                }
            }
        }
    }
}
?>
<main class="login-container">
    <img class="go-back" src="/public/resources/logo.png" alt="logo" onclick="window.location.href = '/'">
    <section class="login-box">
        <h2>Register</h2>
        
        <form action="/?page=register" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your Email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
            <?php if (!empty($error)): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <input type="submit" value="Register">
        </form>
    </section>
</main>