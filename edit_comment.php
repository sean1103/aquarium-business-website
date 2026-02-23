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


  $stmt = $conn->prepare("SELECT * FROM comments WHERE id = :id AND username = :user");
  $stmt->execute([':id' => $commentId, ':user' => $_SESSION["username"]]);
  $comment = $stmt->fetch();

  if (!$comment) {
    die("找不到留言或您無權限編輯。");
  }


  if (isset($_POST['update'])) {
    $updated = trim($_POST['comment']);
    $imagePath = $comment['image_path'];

    if ($_FILES['comment_image']['error'] === UPLOAD_ERR_OK) {
      $uploadDir = 'uploads/';
      $filename = time() . "_" . basename($_FILES['comment_image']['name']);
      $imagePath = $uploadDir . $filename;
      move_uploaded_file($_FILES['comment_image']['tmp_name'], $imagePath);
    }

    $stmt = $conn->prepare("UPDATE comments SET message = :msg, image_path = :img WHERE id = :id");
    $stmt->execute([':msg' => $updated, ':img' => $imagePath, ':id' => $commentId]);

    echo "✅ 留言已更新。<a href='comment.php'>返回留言板</a>";
    exit;
  }

} catch (PDOException $e) {
  die("錯誤：" . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>edit</title>
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
      margin-top: 10px;
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
      width: 50%;
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



<center><body>

  <header>
    <a href="home.php" class="home-link">回首頁</a>
    <a href="cart.php" class="home-link">🛒</a>
    <span class="user-name">
    <?php 
      if (isset($_SESSION["username"])) {
        echo '<a href="order.php" class="home-link">' . htmlspecialchars($_SESSION["username"]) . ' 的頁面</a>';
      } else {
        echo '<a href="login.php" class="home-link">登入</a>';
      }
    ?>
    </span>
  </header>

  
  <h2>編輯留言</h2>
  <form method="post" enctype="multipart/form-data">
    <textarea name="comment" rows="4" required><?= htmlspecialchars($comment['message']) ?></textarea><br>
    <?php if ($comment['image_path']): ?>
      <img src="<?= htmlspecialchars($comment['image_path']) ?>" style="max-width:200px;"><br>
    <?php endif; ?>
    <input type="file" name="comment_image" accept="image/*"><br>
    <button type="submit" name="update">💾 更新</button>
  </form>
</body></center>

</html>
