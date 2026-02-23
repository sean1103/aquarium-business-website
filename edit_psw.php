<?php
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = trim($_POST["username"]);
  $password = trim($_POST["password"]);

  if ($username === "" || $password === "") {
    $message = "請填寫所有欄位！";
  } else {
    $servername = "DESKTOP-0G56S7G\\SQLEXPRESS";
    $dbname     = "final";
    $dbusername = "sa";
    $dbpassword = "StrongPass123!";

    try {
      $conn = new PDO("sqlsrv:Server=$servername;Database=$dbname", $dbusername, $dbpassword);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      
      $stmt = $conn->prepare("SELECT * FROM customer WHERE id = :id");
      $stmt->bindParam(':id', $username, PDO::PARAM_STR);
      $stmt->execute();

      if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        $update = $conn->prepare("UPDATE customer SET psw = :psw WHERE id = :id");
        $update->bindParam(':id', $username, PDO::PARAM_STR);
        $update->bindParam(':psw', $password, PDO::PARAM_STR);
        $update->execute();

        $message = "密碼已修改，請回首頁登入！";
      } else {
        $message = "找不到此帳號！";
      }

    } catch (PDOException $e) {
      $message = "資料庫錯誤：" . $e->getMessage();
    }
  }
}
?>


<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>修改密碼</title>
  <style>
    body {
      background-color: #3e3f40;
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .register-form {
      background-color: white;
      padding: 2em;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      width: 300px;
    }

    .register-form h2 {
      text-align: center;
      margin-bottom: 1em;
    }

    .register-form label {
      display: block;
      margin-bottom: 0.5em;
    }

    .register-form input[type="text"],
    .register-form input[type="password"] {
      width: 100%;
      padding: 0.5em;
      margin-bottom: 1em;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    .register-form button {
      width: 100%;
      padding: 0.7em;
      background-color:rgb(79, 172, 118);
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .register-form button:hover {
      background-color:rgb(49, 219, 120);
    }

    .message {
      text-align: center;
      margin-top: 1em;
      color: red;
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

<div class="register-form">
  <h2>🦈 修改密碼</h2>
  <form method="POST" action="">
    <label for="username">帳號名稱：</label>
    <input type="text" id="username" name="username" required>

    <label for="password">設定新密碼：</label>
    <input type="password" id="password" name="password" required>

    <button type="submit">一鍵修改</button>
  </form>

  <?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <form action="home.php">
    <button type="submit" style="margin-top:1em; background-color:rgb(85, 113, 156);">回首頁</button>
  </form>

  <form action="login.php">
    <button type="submit" style="margin-top:1em; background-color:rgb(85, 113, 156);">回登入</button>
  </form>

</div>

<div id="cursor-icon">🦈</div>

<script>
const cursorIcon = document.getElementById("cursor-icon");
const form = document.querySelector(".register-form"); 

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
