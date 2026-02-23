<?php
session_start();

// 清除所有 session 變數
session_unset();

// 銷毀 session
session_destroy();
?>

<?php
header("Location: home.php"); 
exit();
?>
