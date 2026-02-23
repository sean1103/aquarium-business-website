
<?php
session_start();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>留言板</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <style>
    body {
      background-color: #636669;
      color: white;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }

    header {
      background-color: #333;
      padding: 10px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .home-link {
      color: gray;
      text-decoration: none;
      font-size: 18px;
      margin-right: 15px;
      transition: all 0.3s ease;
    }

    .home-link:hover {
      color: white;
      transform: scale(1.1);
    }

    .container {
      max-width: 700px;
      margin: 20px auto;
      padding: 20px;
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      margin-top: 0px;
    }

    .comment {
      background-color: #444;
      border-radius: 12px;
      padding: 15px;
      margin-bottom: 20px;
      box-shadow: 0 0 8px rgba(0, 0, 0, 0.3);
    }

    .comment p {
      margin: 0 0 10px;
    }

    .comment img {
      max-width: 100%;
      max-height: 200px;
      border: 1px solid #ccc;
      border-radius: 8px;
      margin-top: 10px;
    }

    .comment a {
      color: #ccc;
      text-decoration: none;
      font-size: 14px;
      margin-right: 10px;
    }

    .comment a:hover {
      color: #fff;
    }

    form textarea {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: none;
      resize: vertical;
      margin-bottom: 10px;
    }

    form input[type="file"] {
      margin-bottom: 10px;
    }

    form button {
      background-color: #5cb85c;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
    }

    form button:hover {
      background-color: #4cae4c;
    }

    .login-reminder {
      text-align: center;
      font-size: 16px;
    }

    .login-reminder a {
      color: #ddd;
    }

    .login-reminder a:hover {
      color: white;
    }

            #bgVideo {
        position: fixed;
        right: 0;
        bottom: 0;
        min-width: 100%;
        min-height: 100%;
        z-index: -1;
        object-fit: cover;
        opacity: 0.6;
        filter: brightness(0.5); /* 可選：讓文字更清楚 */
    }

  </style>
</head>

<body>
<video autoplay muted loop id="bgVideo">
    <source src="asses/comment.mp4" type="video/mp4">
</video>
<header>
  <a href="home.php" class="home-link">回首頁</a>
  <a href="cart.php" class="home-link">🛒</a>
  <span class="user-name">
  <?php 
    if (isset($_SESSION["username"])) {
      echo '<a href="order.php" style="color: white; text-decoration: none;">' . htmlspecialchars($_SESSION["username"]) . ' 的頁面</a>';
    } else {
      echo '<a href="login.php" class="home-link">登入</a>';
    }
  ?>
  </span>
</header>

<div class="container">
<?php

try {
  
  require_once 'db_config.php';

  if (isset($_POST['submit_comment']) && isset($_SESSION["username"])) {
    $comment = trim($_POST['comment']);
    $imagePath = null;

    if (isset($_FILES['comment_image']) && $_FILES['comment_image']['error'] === UPLOAD_ERR_OK) {
      $uploadDir = 'uploads/';
      if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
      }
      $filename = time() . "_" . basename($_FILES['comment_image']['name']);
      $targetPath = $uploadDir . $filename;
      move_uploaded_file($_FILES['comment_image']['tmp_name'], $targetPath);
      $imagePath = $targetPath;
    }

    if ($comment !== "") {
      $stmt = $conn->prepare("INSERT INTO comments (username, message, image_path) VALUES (:user, :msg, :img)");
      $stmt->bindParam(':user', $_SESSION["username"]);
      $stmt->bindParam(':msg', $comment);
      $stmt->bindParam(':img', $imagePath);
      $stmt->execute();
    }
  }

  $stmt = $conn->query("SELECT * FROM comments ORDER BY created_at DESC");
  $comments = $stmt->fetchAll();

  echo "<h2>💬 留言板 🌊</h2>";

  foreach ($comments as $c) {
    $user = htmlspecialchars($c['username']);
    $msg = htmlspecialchars($c['message']);
    $time = htmlspecialchars($c['created_at']);
    $img = $c['image_path'];
    $id = $c['id'];

    echo "<div class='comment'>";
    echo "<p><strong>$user</strong>：$msg</p>";
    if (!empty($img)) {
      echo "<img src='" . htmlspecialchars($img) . "' alt='留言圖片'><br>";
    }
    echo "<span style='font-size:12px;color:gray;'>$time</span><br>";

    if (isset($_SESSION["username"]) && $_SESSION["username"] === $user) {
      echo "<a href='edit_comment.php?id=$id'>✏️ 編輯</a>";
      echo "<a href='delete_comment.php?id=$id' onclick='return confirm(\"確定要刪除這則留言嗎？\")'>🗑️ 刪除</a>";
    }

    echo "</div>";
  }

  if (isset($_SESSION["username"])) {
    echo '
      <center><form method="post" enctype="multipart/form-data">
        <textarea name="comment" rows="3" required placeholder="輸入留言..."></textarea><br>
        <input type="file" name="comment_image" accept="image/*"><br>
        <button type="submit" name="submit_comment">🐚確認留言</button>
      </form></center>';
  } else {
    echo "<p class='login-reminder'>🔒 請先 <a href='login.php'>登入</a> 才能留言。</p>";
  }

} catch (PDOException $e) {
  echo "錯誤：" . $e->getMessage();
}
?>
</div>

</body>
</html>
