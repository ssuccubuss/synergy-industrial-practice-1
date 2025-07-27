<?php
require_once 'config.php';
require_once 'auth.php';

logoutUser();
header("Location: " . BASE_URL . "login.php");
exit();
?>