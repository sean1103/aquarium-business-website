<?php
session_start();

if (!isset($_SESSION["customer_id"])) {
    echo '
    <script>
        if (confirm("請先登入才能加入購物車，是否登入?")) {
            window.location.href = "login.php";
        } else {
            history.back();
        }
    </script>
    ';
    exit;
}
$customer_id = $_SESSION["customer_id"];

// 資料庫設定
$servername = "DESKTOP-0G56S7G\\SQLEXPRESS";
$dbname = "final";
$dbusername = "sa";
$dbpassword = "StrongPass123!";

try {
    $conn = new PDO("sqlsrv:Server=$servername;Database=$dbname", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 處理刪除商品
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["remove_product_id"])) {
        $remove_id = $_POST["remove_product_id"];
        
        // 查詢刪除商品的數量
        $quantityStmt = $conn->prepare("SELECT quantity FROM cart WHERE customer_id = ? AND product_id = ?");
        $quantityStmt->execute([$customer_id, $remove_id]);
        $quantity = $quantityStmt->fetch(PDO::FETCH_ASSOC)['quantity'];
        
        // 更新商品庫存
        $updateStockStmt = $conn->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
        $updateStockStmt->execute([$quantity, $remove_id]);
        
        // 刪除購物車中的商品
        $deleteStmt = $conn->prepare("DELETE FROM cart WHERE customer_id = ? AND product_id = ?");
        $deleteStmt->execute([$customer_id, $remove_id]);
    }

    // 查詢購物車內容
    $stmt = $conn->prepare("
        SELECT p.id AS product_id, p.name AS product_name, p.price, p.img_src, c.quantity
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.customer_id = ?
    ");
    $stmt->execute([$customer_id]);

    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 查詢 total_spent
    $spendStmt = $conn->prepare("SELECT total_spent FROM customer WHERE id = ?");
    $spendStmt->execute([$customer_id]);
    $spend = $spendStmt->fetch(PDO::FETCH_ASSOC);
    $total_spent = $spend["total_spent"];

} catch (PDOException $e) {
    echo "資料庫錯誤：" . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>🛒</title>
    <style>

        body {
            background-color:rgb(99, 102, 105);
            color: white;
            font-family: Arial, sans-serif;
            padding: 0;
            margin: 0;
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
            font-size: 18px;
            color: #ccc;
        }

        h1 {
            text-align: center;
            padding: 20px 0;
        }

        .cart-container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }

        .cart-item {
            background-color: #2e2e3e;
            border-radius: 10px;
            margin-bottom: 15px;
            padding: 15px;
            display: flex;
            align-items: center;
        }

        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 20px;
        }

        .item-details {
            flex-grow: 1;
        }

        .item-details h3 {
            margin: 0 0 5px 0;
        }

        .item-actions {
            text-align: right;
        }

        .item-actions form {
            display: inline;
        }

        .btn {
            padding: 6px 12px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-remove {
            background-color:rgb(195, 62, 62);
            color: white;
            opacity: 0.6;
        }

        .btn-remove:hover {
            opacity: 1;
            transform: scale(1.1);
        }

        .btn-checkout {
            background-color:rgb(49, 126, 68);
            color: white;
            opacity: 0.5;
        }

        .btn-checkout:hover {
            opacity: 1;
            transform: scale(1.1);
        }

        .summary {
            text-align: center;
            margin-top: 20px;
        }

        .sum{
            text-align: center;
            margin-top: px;
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
            white-space: nowrap; /* 不換行 */
            font-size: 15px; 
            color: white;
            border: 2px solid gray;
            border-radius: 3px;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1000;
            pointer-events: none;
            transform: translateX(-50%) scaleX(-1);
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

        #bgVideo {
        position: fixed;
        right: 0;
        bottom: 0;
        min-width: 100%;
        min-height: 100%;
        z-index: -1;
        object-fit: cover;
        opacity: 0.4;
        filter: brightness(0.5); 
    }

    </style>

</head>
<body>
<video autoplay muted loop id="bgVideo">
    <source src="asses/f3.mp4" type="video/mp4">
</video>
<header>
  <a href="home.php" class="home-link">回首頁</a>
  <a href="order.php" class="home-link">查看已成交訂單</a>
  <a href="logout.php" class="home-link" onclick="logout()">登出</a>
</header>

<h1 style="color: white;margin-bottom: 2px;">
  <?php echo htmlspecialchars($_SESSION["username"]) . " 的購物車 🛒"; ?>
</h1>

<div class="cart-container">
<?php
$total = 0;
if (count($cart_items) === 0) {
    echo '
    <center>
        <div class="hover-container">
            <a href="products/equipment.php" class="emoji-left">🌊</a>
            <a href="products/fish_food.php" class="emoji-left">🐚</a>
            <p class="hover-text">購物車是空的，趕快去下單東西吧!</p>
            <a href="products/fish.php" class="emoji-right">🐠</a>
            <a href="products/coral.php" class="emoji-right">🪸</a>
            <a href="products/others.php" class="emoji-right">🦐</a>
        </div>
    </center>
    
    <style>
        .hover-container {
            display: inline-flex;
            align-items: center;
            position: relative;
            transition: all 0.3s ease;
        }
    
        .hover-text {
            margin: 0 20px; /* 保留左右空間給 emoji */
            transition: all 0.3s ease;
            white-space: nowrap;
        }
    
        .emoji-left, .emoji-right {
            font-size: 1.5em;
            opacity: 0;
            transform: translateX(0);
            transition: all 0.4s ease;
            text-decoration: none;
        }
    
        .emoji-left {
            margin-right: -30px; /* emoji 初始藏到左邊 */
        }
    
        .emoji-right {
            margin-left: -30px; /* emoji 初始藏到右邊 */
        }
    
        .hover-container:hover .emoji-left {
            opacity: 1;
            transform: translateX(-10px);
            margin-right: 10px;
        }
    
        .hover-container:hover .emoji-right {
            opacity: 1;
            transform: translateX(10px);
            margin-left: 10px;
        }
    </style>
    ';
} else {
    foreach ($cart_items as $item) {
        $subtotal = $item["price"] * $item["quantity"];
        $total += $subtotal;

        // 修正圖片路徑
        $img_path = preg_replace('/^(\.\.\/|\.\/)?/', '', $item["img_src"]);
        echo '
        <div class="cart-item">
            <img src="' . htmlspecialchars($img_path) . '" alt="商品圖片">
            <div class="item-details">
                <h3>' . htmlspecialchars($item["product_name"]) . '</h3>
                <p>數量: ' . $item["quantity"] . '</p>
                <p>單價: $' . $item["price"] . ' - 小計: $' . $subtotal . '</p>
            </div>
            <div class="item-actions">
                <form method="POST">
                    <input type="hidden" name="remove_product_id" value="' . $item["product_id"] . '">
                    <button type="submit" class="btn btn-remove">從購物車移除</button>
                </form>
            </div>
        </div>';
    }

    echo '<div class="summary">
        <h2>小計: $' . $total . '</h2>
        <form method="POST" action="checkout.php">
            <input type="hidden" name="checkout_total" value="' . $total . '">
            <button type="submit" class="btn btn-checkout">💳 確定購買</button>
        </form>
    </div>';
}
?>
</div>

<a href="order.php">
    <div class="swimming-fish-container">
      <img src="asses/fish2.png" class="swimming-fish" alt="登入魚">
      <p class="fish-text">
        <?php
        echo '💰過去消費總額：$' . floor($total_spent);
        ?>
    </p>
    </div>
  </a>
</body>

</html>