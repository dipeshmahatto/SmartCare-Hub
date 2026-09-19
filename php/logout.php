<?php
require_once 'security.php';
secure_session_start();
session_unset();
session_destroy();
header("Location: index.php");
?>