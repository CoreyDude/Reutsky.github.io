<?php
//выход из акка
session_start();
session_destroy();
header('Location: login.php');
exit;
?>