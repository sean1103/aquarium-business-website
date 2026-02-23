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

// 獲取商品名稱
if (isset($_GET['name'])) {
    $product_name = $_GET['name'];

    // SQL 查詢庫存數量
    $sql = "SELECT quantity FROM products WHERE name = :product_name";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':product_name', $product_name, PDO::PARAM_STR);
    $stmt->execute();

    // 取得結果
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo $result['quantity']; // 回傳庫存數量
    } else {
        echo "無此商品"; // 若商品不存在
    }
} else {
    echo "未指定商品名稱";
}
?>
