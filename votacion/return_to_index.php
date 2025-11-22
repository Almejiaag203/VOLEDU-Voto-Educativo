<?php
session_start();
unset($_SESSION['dni']);
unset($_SESSION['votado']);
header("Location: index.php");
exit;
?>