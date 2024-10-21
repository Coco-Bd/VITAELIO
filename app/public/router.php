<?php
// Capture the requested URL path
$request = $_SERVER['REQUEST_URI'];
$url_components = parse_url($request);

// Initialize variables
$params = [];
if (isset($url_components['query'])) {
    parse_str($url_components['query'], $params);
}

error_log("Requested URL path: " . $request);

// Initialize variables
$page = '';
$includeHeaderFooter = true;

// Route the request to the appropriate handler
$pageParam = $params['page'] ?? '';
switch ($pageParam) {
    case '':
    case 'index':
    case '/':
        $page = 'pages/landing.php';
        break;
    case 'register':
        $page = 'pages/register.php';
        $includeHeaderFooter = false;
        break;
    case 'login':
        $page = 'pages/login.php';
        $includeHeaderFooter = false;
        break;
    case 'dashboard':
        $page = 'pages/dashboard.php';
        break;
    default:
        http_response_code(404);
        $page = 'pages/404.php';
        break;   
}

// Log the selected page for debugging
error_log("Selected page: " . $page);

// Include the header if needed
if ($includeHeaderFooter) {
    include __DIR__ . '/utils/header.php';
}

// Include the selected page
include __DIR__ . '/' . $page;

// Include the footer if needed
if ($includeHeaderFooter) {
    include __DIR__ . '/utils/footer.php';
}