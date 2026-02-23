<?php
session_start();

// 設定資料庫連接資訊
$servername = "DESKTOP-0G56S7G\\SQLEXPRESS";
$dbname = "final";
$dbusername = "sa";
$dbpassword = "StrongPass123!";

// 建立資料庫連接
try {
    $conn = new PDO("sqlsrv:server=$servername;Database=$dbname", $dbusername, $dbpassword);
    // 設定 PDO 錯誤模式
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "資料庫連線失敗: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>🦐</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: rgb(99, 102, 105);
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
        }

        .home-link:hover {
            color:rgb(255, 255, 255);
            transform: scale(1.1);
        }

    .user-name {
      font-size: 16px;
      font-weight: bold;
    }

    h1 {
      margin: 20px 0;
      text-align: center;
      color: #2c3e50;
    }

    .fish-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      padding: 20px;
      max-width: 1200px;
      margin: auto;
    }

    .fish-card {
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      padding: 15px;
      text-align: left;
    }

    .fish-card img {
      width: 100%;
      height: 160px;
      object-fit: cover;
      border-radius: 8px;
    }

    .fish-info {
      margin-top: 10px;
    }

    .fish-info p {
      margin: 4px 0;
      font-size: 14px;
    }

    .add-to-cart {
      margin-top: 10px;
      background-color: rgb(161, 170, 175);
      color: white;
      padding: 8px 12px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
      width: 100%;
    }

    .add-to-cart:hover {
      background-color: rgb(75, 84, 92);
    }
  </style>
</head>
<body>

<header>
  <a href="../home.php" class="home-link">回首頁</a>
  <a href="../cart.php" class="home-link">🛒</a>
  <span class="user-name">
  <?php 
  if (isset($_SESSION["username"])) {
    echo '<a href="../order.php" style="color: white; text-decoration: none;">' . htmlspecialchars($_SESSION["username"]) . ' 的頁面</a>';
  } else {
    echo '<a href="../login.php" class="home-link" >登入</a>';
  }
?>
  </span>
</header>

<h1 style="color: white;">🦐 其他生物販售區</h1>

<div class="fish-grid">
  <?php
  $fishes = [
    ["img" => "../asses/others/o1.jpg", "name" => "清潔蝦", "size" => "8cm", "price" => "$200", "quantity" => "5"],
    ["img" => "../asses/others/o2.jpg", "name" => "假綿羊蝦", "size" => "12cm", "price" => "$50", "quantity" => "6"],
    ["img" => "../asses/others/o3.jpg", "name" => "拳擊蟹", "size" => "5cm", "price" => "$150", "quantity" => "10"],
    ["img" => "../asses/others/o4.jpg", "name" => "翻砂海星", "size" => "6cm", "price" => "$100", "quantity" => "2"],
    ["img" => "../asses/others/o5.jpg", "name" => "五爪貝", "size" => "8cm", "price" => "$3000", "quantity" => "5"],
    ["img" => "../asses/others/o6.jpg", "name" => "食藻螺", "size" => "4cm", "price" => "$20", "quantity" => "5"],
    ["img" => "../asses/others/o7.jpg", "name" => "性感蝦", "size" => "5cm", "price" => "$100", "quantity" => "2"],
    ["img" => "../asses/others/o8.jpg", "name" => "海膽", "size" => "5cm", "price" => "$100", "quantity" => "2"],
    ["img" => "../asses/others/o9.jpg", "name" => "海兔子", "size" => "3cm", "price" => "$150", "quantity" => "2"],
    ["img" => "../asses/others/o10.jpg", "name" => "海寄居蟹", "size" => "3cm", "price" => "$200", "quantity" => "2"],
  ];

  foreach ($fishes as $fish) {
    echo '<div class="fish-card">';
    echo '<img src="' . $fish["img"] . '" alt="' . $fish["name"] . '">';
    echo '<div class="fish-info">';
    echo '<p><strong>' . $fish["name"] . '</strong></p>';
    echo '<p>大小: ' . $fish["size"] . '</p>';
    echo '<p>價格: ' . $fish["price"] . '</p>';
    echo '</div>';
    if (isset($_SESSION["username"])) {
      echo '<button class="add-to-cart" onclick="handleAddToCart(\'' . $fish["name"] . '\')">🛒加入購物車 !</button>';
    } else {
      echo '<button class="add-to-cart" onclick="redirectToLogin()">🛒加入購物車 !</button>';
    }
    echo '<button class="add-to-cart" onclick="getQuantityByName(\'' . $fish["name"] . '\')">🔍庫存數量查詢</button>';
    echo '</div>';
  }
  ?>
</div>

<script>
function handleAddToCart(fishName) {
  fetch('../add_to_cart.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'product_name=' + encodeURIComponent(fishName)
  })
  .then(response => response.text())
  .then(data => {
    if (data === 'success') {
      alert(fishName + ' 已加入購物車!');
    } else {
      alert(data);
    }
  })
  .catch(error => {
    alert('傳送錯誤：' + error);
  });
}

function redirectToLogin() {
  if (confirm("請先登入才能加入購物車，是否登入?")) {
    window.location.href = "../login.php";
  }
}

function getQuantityByName(fishName) {
  fetch('../get_quantity.php?name=' + encodeURIComponent(fishName))
    .then(response => response.text())
    .then(quantity => {
      alert(fishName + ' 剩餘庫存：' + quantity);
    })
    .catch(error => {
      alert('錯誤: ' + error);
    });
}
</script>

</body>
</html>
