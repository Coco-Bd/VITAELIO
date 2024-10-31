<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

 if (!empty($first_name) && !empty($last_name)) {
        $stmt = $db->prepare('SELECT user_firstName = :first_name, user_lastName = :last_name FROM users WHERE id = :user_id');
        $stmt->bindValue(':first_name', $first_name, SQLITE3_TEXT);
        $stmt->bindValue(':last_name', $last_name, SQLITE3_TEXT);
        $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
        $stmt->execute();

        // Update session variables
        $_SESSION['user_firstName'] = $first_name;
        $_SESSION['user_lastName'] = $last_name;
    }

$user_firstName = isset($_SESSION['user_firstName']) ? $_SESSION['user_firstName'] : 'Guest';
$user_lastName = isset($_SESSION['user_lastName']) ? $_SESSION['user_lastName'] : '';

?>
<footer>
    <div class="footer-content">
        <p><a href="/?page=contact">Contact</a></p>
        <p>User: <?php echo htmlspecialchars($user_firstName) . ' ' . htmlspecialchars($user_lastName); ?></p>
    </div>
    <p>&copy; 2023 VITAELIO. All rights reserved.</p>
</footer>

<style>
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        margin: 0;
    }

    main {
        flex: 1;
    }

    footer {
        margin-top: 20px;
        background-color: var(--pico-muted);
        color: var(--pico-contrast);
        text-align: center;
        padding: 20px;
        border-top: 1px solid var(--pico-contrast-border);
        position: relative;
        bottom: 0;
        width: 100%;
    }

    .footer-content {
        display: flex;
        justify-content: space-between;
        max-width: 800px;
        margin: auto;
    }

    .footer-content a {
        color: var(--pico-primary);
        text-decoration: none;
    }

    .footer-content a:hover {
        text-decoration: underline;
    }
</style>