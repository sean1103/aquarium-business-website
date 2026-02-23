<?php
session_start();

if (!isset($_GET['id']) || !isset($_SESSION["username"])) {
  die("未授權的存取");
}

$commentId = (int)$_GET['id'];
$servername = "DESKTOP-0G56S7G\\SQLEXPRESS";
$dbname = "final";
$dbusername = "sa";
$dbpassword = "StrongPass123!";

try {
  $conn = new PDO("sqlsrv:Server=$servername;Database=$dbname", $dbusername, $dbpassword);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $conn->prepare("DELETE FROM comments WHERE id = :id AND username = :user");
  $stmt->execute([':id' => $commentId, ':user' => $_SESSION["username"]]);

  header("Location: comment.php");
  exit;

} catch (PDOException $e) {
  die("錯誤：" . $e->getMessage());
}
