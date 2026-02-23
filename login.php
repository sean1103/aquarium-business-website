<?php
session_start();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = trim($_POST["username"]);
  $password = trim($_POST["password"]);


  if ($username === "" || $password === "") {
    $message = "請填寫所有欄位！";
  } else {

    require_once 'db_config.php';

    try {
      
      $stmt = $conn->prepare("SELECT * FROM customer WHERE id = :id");
      $stmt->bindParam(':id', $username);
      $stmt->execute();

      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($user && $password === $user['psw']) {
        $_SESSION['username'] = $username;
        $_SESSION["customer_id"] = $user["id"];
        header("Location: home.php");
        exit();
      } else {
        $message = "請重新檢查帳號密碼 !!";
      }

    } catch (PDOException $e) {
      $message = "資料庫連線失敗：" . $e->getMessage();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>登入頁面</title>
  <style>

    
    body {
      background-color: rgb(62, 63, 64);
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-form {
      background-color: white;
      padding: 2em;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      width: 300px;
    }

    .login-form h2 {
      text-align: center;
      margin-bottom: 1em;
    }

    .login-form label {
      display: block;
      margin-bottom: 0.5em;
    }

    .login-form input[type="text"],
    .login-form input[type="password"] {
      width: 100%;
      padding: 0.5em;
      margin-bottom: 1em;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    .login-form button {
      width: 100%;
      padding: 0.7em;
      background-color: rgb(111, 173, 214);
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .login-form button:hover {
      background-color: rgb(67, 112, 142);
    }

    .message {
      text-align: center;
      margin-top: 1em;
      color: red;
    }

    .register-btn {
      width: 100%;
      padding: 0.7em;
      background-color: #2ecc71;
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      margin-top: 1em;
    }

    .register-btn:hover {
      background-color: #27ae60;
    }

    #cursor-icon {
    position: absolute;
    pointer-events: none;
    font-size: 24px;
    z-index: 999;
    display: block;
  }

  </style>
</head>
<body>

<div class="login-form">
  <h2>🐠 會員登入入口</h2>
  <form method="POST" action="">
    <label for="username">帳號：</label>
    <input type="text" id="username" name="username" required>

    <label for="password">密碼：</label>
    <input type="password" id="password" name="password" required>

    <button type="submit">登入</button>
  </form>

  <?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <form action="register.php">
    <button type="submit" class="register-btn">創建新帳號</button>
  </form>

  <form action="edit_psw.php">
    <button type="submit" class="register-btn">修改密碼</button>
  </form>

  <form action="home.php">
  <button type="submit" class="register-btn">回首頁</button>
  </form>

</div>


<div id="cursor-icon">🐠</div>

<script>
  const cursorIcon = document.getElementById("cursor-icon");
  const form = document.querySelector(".login-form");

  document.addEventListener("mousemove", (e) => {
    cursorIcon.style.left = e.pageX + 10 + "px";
    cursorIcon.style.top = e.pageY + 10 + "px";
  });

  form.addEventListener("mouseenter", () => {
    cursorIcon.style.display = "none";
  });

  form.addEventListener("mouseleave", () => {
    cursorIcon.style.display = "block";
  });
</script>


</body>
</html>
