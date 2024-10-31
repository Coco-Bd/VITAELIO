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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['data'])) {
    $data = $_POST['data'];
    $stmt = $db->prepare('INSERT INTO cv_data (user_id, data) VALUES (:user_id, :data)');
    $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
    $stmt->bindValue(':data', $data, SQLITE3_TEXT);
    $stmt->execute();
}

// Fetch CV data
$query = $db->prepare('SELECT id, title, description FROM cv_data WHERE user_id = :user_id');
$query->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
$result = $query->execute();
?>
<style>
    main {
        overflow: hidden;
    }
    #main_container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-family: Arial, sans-serif;
    }
    .cv-card {
        border: var(--pico-border-width) solid var(--pico-contrast-border);
        border-radius: var(--pico-border-radius);
        padding: 15px;
        margin: 20px 0;
        
        list-style-type: none;
        display: flex;
        flex-direction: column;
        justify-content: center;
        flex-wrap: wrap;
        overflow: hidden;
    }
     #description_cv {
        word-wrap: break-word; 
        overflow: hidden;
        text-overflow: ellipsis; 
        white-space: normal; 
        font-family: Arial, sans-serif;
        max-width: 100%;
    }

    .container #create_cvLink {
        text-decoration: none;
        background-color: var(--pico-contrast-background);
        color: var(--pico-contrast-inverse);
        padding: 10px 20px;
        border-radius: var(--pico-border-radius);
        border: var(--pico-border-width);
    } & p {
        text-align: center;
        margin-block: 30px;
    }

    #scrollable {
        margin-top: 30px;
        overflow-y: auto;
        height: 500px;
    }

    ::-webkit-scrollbar {
        display: none;
    }

    ul li {
        margin: 10px 0;
    }
    ul {
        padding: 0;
    }
    #submit-button {
        max-width: 150px;
    }


</style>
<main id="main_container">
    <h2>Your CVs</h2>

    <div class="container">
        <a id="create_cvLink" href="/?page=create_cv">Create New CV</a>
    </div>
    
    <div class="container">
        <section id="scrollable">
            <ul>
            <?php while ($row = $result->fetchArray(SQLITE3_ASSOC)): ?>
                <li class="cv-card">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p id="description_cv"><?php echo htmlspecialchars($row['description']); ?></p>
                    
                
                    
                <form action="/?page=set_cv_id" method="POST" style="display:inline;">

                        <input type="hidden" name="cv_id" value="<?php echo $row['id']; ?>">
                        <button id="submit-button" type="submit" style="margin-bottom: 0;">Edit CV</button>
                </form>    
            </li>
            <?php endwhile; ?>
            </ul>
        </section>
    </div>
</main>