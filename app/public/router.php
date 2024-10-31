<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


$pageParam = isset($_GET['page']) ? $_GET['page'] : '/';


$page = '';
$includeHeader = true;
$includeFooter = true;


switch ($pageParam) {
    case '':
    case 'index':
    case '/':
        $page = 'pages/landing.php';
        break;
    case 'register':
        $page = 'pages/register.php';
        $includeHeader = false;
        $includeFooter = false;
        break;
    case 'login':
        $page = 'pages/login.php';
        $includeHeader = false;
        $includeFooter = false;
        break;
    case 'dashboard':
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: /?page=login");
            exit();
        }
        $page = 'pages/dashboard.php';
        break;
    case 'cv_update':
        $page = 'pages/cv_update.php';
        break;
        case 'create_cv':
        $page = 'pages/create_cv.php';
        break;
         case 'set_cv_id':
        $page = 'set_cv_id.php';
        break;
        case 'contact':
        $page = 'pages/contact.php';
        $includeFooter = false;

        break;
        case 'profile':
        $page = 'pages/profile.php';
        break;
        case 'portfolio':
        $page = 'pages/portfolio.php';
        break;
    default:
        http_response_code(404);
        $page = 'pages/404.php';
        break;   
}

// Log the selected page for debugging
error_log("Selected page: " . $page);

// Include the header if needed
if ($includeHeader) {
    include __DIR__ . '/utils/header.php';
}

// Include the selected page
include __DIR__ . '/' . $page;

// Include the footer if needed
if ($includeFooter) {
    include __DIR__ . '/utils/footer.php';
}