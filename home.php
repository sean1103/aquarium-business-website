<?php
session_start();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>海水魚買賣網</title>
  <style>

    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: rgb(8, 8, 8);
      overflow-x: hidden;
    }

    nav {
      background-color: rgb(91, 91, 92);
      opacity: 75%;
      color: white;
      padding: 1em;
      display: flex;
      justify-content: space-between;
    }

    nav a {
      color: white;
      text-decoration: none;
      font-weight: bold;
      padding: 0.5em 1em;
      border-radius: 5px;
      transition: all 0.3s ease;
    }

    nav a:hover {
      color: rgb(255, 255, 255);
      background-color: rgba(0, 0, 0, 0.99);
    }

    .dropdown {
      position: relative;
      display: inline-block;
    }

    .dropdown-content {
      position: absolute;
      top: 100%;
      left: 0;
      background-color: rgba(122, 116, 116, 0.95);
      min-width: 160px;
      display: flex;
      flex-direction: column;
      box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
      opacity: 0;
      transform: translateY(-10px);
      pointer-events: none;
      transition: all 0.3s ease;
      z-index: 1000;
    }

    .dropdown:hover .dropdown-content {
      opacity: 1;
      transform: translateY(0);
      pointer-events: auto;
    }

    .dropdown > a {
      color: white;
      padding: 0.7em 1.2em;
      border-radius: 5px;
      font-size: 16px;
      font-weight: bold;
      text-decoration: none;
      transition: background-color 0.3s ease;
      display: inline-block;
    }


    .main-banner {
      width: 100%;
      height: 320px;
      overflow: hidden;
      position: relative;
    }

    .main-banner video {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .login-btn, .logout-btn {
      background-color: rgb(156, 188, 221);
      color: white;
      padding: 0.7em 1.2em;
      border-radius: 5px;
      font-size: 16px;
      font-weight: bold;
      text-decoration: none;
      transition: background-color 0.3s ease;
    }

    .login-btn:hover, .logout-btn:hover {
      background-color: rgb(31, 126, 204);
    }

    .cart{
      background-color: rgb(244, 194, 183);
      color: white;
      padding: 0.7em 1.2em;
      border-radius: 5px;
      font-size: 16px;
      font-weight: bold;
      text-decoration: none;
      transition: background-color 0.3s ease;
    }

    .cart:hover {
      background-color: rgb(255, 136, 50);
    }

    .search-container {
      position: absolute;
      top: 20px;
      right: 20px;
      display: flex;
      background-color: rgba(255, 240, 240, 0.8);
      border-radius: 8px;
      padding: 5px;
      align-items: center;
    }

    .search-container input[type="text"] {
      border: none;
      padding: 8px;
      border-radius: 5px 0 0 5px;
      font-size: 14px;
    }

    .search-container button {
      background-color: rgb(122, 163, 203);
      border: none;
      color: white;
      padding: 8px 12px;
      border-radius: 0 5px 5px 0;
      cursor: pointer;
      font-weight: bold;
    }

    .search-container button:hover {
      background-color: #0066cc;
    }


    .swimming-fish-container {
      position: fixed;
      bottom: 5%;
      left: -80px;
      width: 70px;
      z-index: 999;
      animation: swim-left-right 50s infinite;
    }

    .swimming-fish {
      width: 100%;
      height: auto;
      display: block;
    }

    .fish-text {
      position: absolute;
      bottom: 70%;
      left: 50%;
      transform: translateX(-50%);
      color: white; 
      opacity: 0;
      transition: opacity 0.3s ease;
      font-size: 16px;
      z-index: 1000;
    }

    .swimming-fish-container:hover .fish-text {
      opacity: 1;
    }

    @keyframes swim-left-right {
      0% {
        left: -80px;
        transform: scaleX(-1);
      }
      49.999% {
        transform: scaleX(-1);
      }
      50% {
        left: 100vw;
        transform: scaleX(1);
      }
      99.999% {
        transform: scaleX(1);
      }
      100% {
        left: -80px;
        transform: scaleX(-1);
      }
    }

  
  footer {
  position: fixed;
  bottom: 0;
  width: 100%;
  text-align: center;
  color: white;
  font-size: 10px;
  }
  

  </style>
  
</head>


<body>

  <!-- 魚移動標 -->
  <?php if (isset($_SESSION['username'])): ?>
  <a href="products/fish.php">
    <div class="swimming-fish-container">
      <img src="asses/fish2.png" class="swimming-fish" alt="登入魚">
      <p class="fish-text">🔓</p>
    </div>
  </a>
<?php else: ?>
  <a href="products/fish.php">
    <div class="swimming-fish-container">
      <img src="asses/fish.png" class="swimming-fish" alt="未登入魚">
      <p class="fish-text">🔒</p>
    </div>
  </a>
<?php endif; ?>



  <div class="main-banner">
    <video autoplay muted loop playsinline>
      <?php if (isset($_SESSION['username'])): ?>
        <source src="asses/fish2.mp4" type="video/mp4">
      <?php else: ?>
        <source src="asses/f1.mp4" type="video/mp4">
      <?php endif; ?>
    </video>

    <form method="POST" action="">
      <div class="search-container">
        <input type="text" name="search_term" placeholder="搜尋商品...">
        <button type="submit">搜尋</button>
      </div>
    </form>

    <?php
      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $searchTerm = trim($_POST["search_term"]);

        if ($searchTerm === "") {
          $message = "請輸入搜尋關鍵字！";
        } else {
          
          require_once 'db_config.php';

          try {
            

            // 使用 LIKE 查詢，支援模糊搜尋
            $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE :search");
            $likeTerm = "%" . $searchTerm . "%";
            $stmt->bindParam(':search', $likeTerm);
            $stmt->execute();

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($results) > 0) {
            $message = "";
            foreach ($results as $product) {
              $message .= "🐠找到商品: ". htmlspecialchars($product['name']) ." (". htmlspecialchars($product['path']) . ") ！\n";
            }
            echo "<script>alert(`$message`);</script>";
          } else {
            echo "<script>alert('🐠查無結果');</script>";
          }

          } catch (PDOException $e) {
            echo "資料庫錯誤：" . $e->getMessage();
          }
        }
      }
      ?>

  </div>


  <!-- 導覽bar -->
  <nav>

    <div class="dropdown">
    <a href="#">生物販售</a>
    <div class="dropdown-content">
      <a href="products/fish.php">🐠 海水魚</a>
      <a href="products/coral.php">🪸 珊瑚</a>
      <a href="products/others.php">🦐 其他</a>
    </div>
    </div>
    <div class="dropdown">
    <a href="#">水族用品與飼料</a>
    <div class="dropdown-content">
      <a href="products/equipment.php">🌊 水族用品</a>
      <a href="products/fish_food.php">🐚 海水魚補給</a>
    </div>
    </div>

    <?php if (isset($_SESSION['username'])): ?>

      <!-- 如果已登入，顯示 Cart -->
      <a href="cart.php" class="cart">購物車</a>


      <!-- 顯示登出按鈕 -->
      <a href="logout.php" class="logout-btn" onclick="logout()">登出</a>
      <script type="text/javascript">
        function logout() {
          alert("🐠 您已登出!!");
        }
        
      </script>


    <?php else: ?>
      <!-- 未登入時顯示登入按鈕 -->
      <a href="login.php" class="login-btn">登入</a>
    <?php endif; ?>

    <a href="comment.php">評論區</a>
    <a href="fish_tank.php">更多資訊</a>
  </nav>

  <br>
  <center>
    <div>
      <p style="color: white; font-size: 16px; line-height: 1.6;">
        <br>
        因為我們熱愛海洋，所以我們創建了這個網頁，將一片海洋帶入您的空間。
        <br><br>
        🌊 我們希望您能從房間的一角感受到海洋的歡樂與寧靜。
        所以，潛入其中，享受大海的美好吧！ 🌊
      </p>
    </div>
  </center>


  <?php if (isset($_SESSION['username'])): ?>
    <div style="text-align: center; color: white; font-size: 20px; margin-top: 20px;">
      <p>歡迎, <?php echo htmlspecialchars($_SESSION['username']); ?> !!</p>
      <!-- <p><?php echo "customer_id：" . $_SESSION["customer_id"]; ?></p> -->
    </div>
  <?php endif; ?>

  <footer> 
    <center><p style="color:white">🪸made by 612530020🪸</p></center>
  </footer>

</body>
</html>
