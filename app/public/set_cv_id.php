<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cv_id'])) {
    $_SESSION['cv_id'] = $_POST['cv_id'];
    header('Location: /?page=cv_update');
    exit();
} else {
    exit('Invalid request');
}