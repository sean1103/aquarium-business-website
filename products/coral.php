<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>🪸</title>
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

<h1 style="color: white;">🪸 珊瑚販售區</h1>

<div class="fish-grid">
  <?php
  $fishes = [
    ["img" => "../asses/corals/c1.png", "name" => "米粉珊瑚", "size" => "8cm", "price" => "$2000", "quantity" => "5"],
    ["img" => "../asses/corals/c2.png", "name" => "腦珊瑚", "size" => "12cm", "price" => "$10000", "quantity" => "5"],
    ["img" => "../asses/corals/c3.png", "name" => "單胞珊瑚", "size" => "5cm", "price" => "$1500", "quantity" => "5"],
    ["img" => "../asses/corals/c4.png", "name" => "馬蹄花珊瑚", "size" => "6cm", "price" => "$200", "quantity" => "5"],
    ["img" => "../asses/corals/c5.png", "name" => "香菇珊瑚", "size" => "8cm", "price" => "$300", "quantity" => "5"],
    ["img" => "../asses/corals/c6.jpg", "name" => "榔頭珊瑚", "size" => "4cm", "price" => "$200", "quantity" => "5"],
    ["img" => "../asses/corals/c7.jpg", "name" => "珍珠珊瑚", "size" => "15cm", "price" => "$6000", "quantity" => "5"],
    ["img" => "../asses/corals/c8.jpg", "name" => "草皮珊瑚", "size" => "8cm", "price" => "$150", "quantity" => "5"],
    ["img" => "../asses/corals/c9.jpg", "name" => "花環珊瑚", "size" => "7cm", "price" => "$250", "quantity" => "5"],
    ["img" => "../asses/corals/c10.jpg", "name" => "奶嘴海葵", "size" => "3cm", "price" => "$200", "quantity" => "5"],
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
