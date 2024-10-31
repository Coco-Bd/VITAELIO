<?php
ob_start();
include './public/router.php';
$content = ob_get_clean();

$page = $_GET['page'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.purple.min.css">
    <link rel="stylesheet" href="/public/static/landing.css">
    <link rel="stylesheet" href="/public/static/style.css">
    <?php if ($page === 'create_cv' || $page === 'cv_update' || $page === 'portfolio'): ?>
        <link rel="stylesheet" href="/public/static/cv_styles.css">
    <?php endif; ?>
    <?php if ($page === 'portfolio'): ?>
        <link rel="stylesheet" href="/public/static/project_style.css">
    <?php endif; ?>
    
    <link href="https://fonts.cdnfonts.com/css/segoe-ui-4" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="/public/resources/favicon.ico">
    <title>VITAELIO</title>
</head>
<body>
    <main>
        <?php echo $content; ?>
    </main>

    <script src="/public/utils/scripts.js"></script>
</body>
</html>