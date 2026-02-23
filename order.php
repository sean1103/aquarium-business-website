<?php
session_start();

if (!isset($_SESSION["customer_id"])) {
    echo "請先登入";
    exit;
}

$customer_id = $_SESSION["customer_id"];

$servername = "DESKTOP-0G56S7G\\SQLEXPRESS";
$dbname = "final";
$dbusername = "sa";
$dbpassword = "StrongPass123!";

try {
    $conn = new PDO("sqlsrv:Server=$servername;Database=$dbname", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 查詢該客戶的所有歷史訂單
    $stmt = $conn->prepare("
        SELECT o.product_id, o.quantity, o.price, o.order_time, o.status,
               p.name AS product_name, p.img_src
        FROM orders o
        JOIN products p ON o.product_id = p.id
        WHERE o.customer_id = ?
        ORDER BY o.order_time DESC
    ");
    $stmt->execute([$customer_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "資料庫錯誤：" . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🧾</title>
    <style>
        body {
            background-color: rgb(99, 102, 105);
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

        .order-container {
            padding: 40px;
            white-space: nowrap;
            overflow-x: auto;
            max-width: 800px;
            margin: 0 auto;
        }

        .order-card {
            background-color: gray;
            border-radius: 10px;
            padding: 10px 15px;
            margin-bottom: 6px;
            white-space: nowrap; 
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .order-details {
            display: inline;  /* 讓所有文字排成一行 */
            font-size: 14px;
        }
        .order-details h3 {
            display: inline;
            margin-right: 10px;
        }
        .order-details p {
            display: inline;
            margin: 0 10px 0 0;
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
    <a href="logout.php" class="home-link" onclick="logout()">登出</a>
</header>

<center><div class="order-container">
    <h2><?php echo htmlspecialchars($_SESSION["username"]) . " 的過去訂單紀錄🧾"; ?></h2>

    <?php if (count($orders) === 0): ?>
        <p>尚無訂單紀錄</p>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-card">
                <div class="order-details">
                    <h3>🐚 <?= htmlspecialchars($order['product_name']) ?></h3>
                    <p>數量：<?= $order['quantity'] ?>　單價：<?= $order['price'] ?> 元</p>
                    <p>小計：<?= $order['quantity'] * $order['price'] ?> 元</p>
                    <p>時間：<?= date('Y-m-d H:i', strtotime($order['order_time'])) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div></center>

</body>
</html>
