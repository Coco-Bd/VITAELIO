<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$db = new SQLite3('users.db');
$user_id = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = $_POST['data'];
    $stmt = $db->prepare('INSERT INTO user_data (user_id, data) VALUES (:user_id, :data)');
    $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
    $stmt->bindValue(':data', $data, SQLITE3_TEXT);
    $stmt->execute();
}
$result = $db->query('SELECT * FROM user_data WHERE user_id = ' . $user_id);
?>

    <h1>Dashboard</h1>
<form method="POST">
    Data: <input type="text" name="data" required>
    <button type="submit">Save</button>
</form>
<h2>Your Data</h2>
<ul>
    <?php
    if (!$result) {
        error_log("Failed to execute statement: " . $db->lastErrorMsg());
        echo "Failed to execute statement.";
        exit();
    } 
    while ($row = $result->fetchArray(SQLITE3_ASSOC)): ?>
        <li><?php echo htmlspecialchars($row['data']); ?></li>
    <?php endwhile; ?>
</ul>
