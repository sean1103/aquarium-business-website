<?php
session_start();
if (!isset($_SESSION["customer_id"])) {
    echo "請先登入";
    exit;
}

$customer_id = $_SESSION["customer_id"];
$purchase_time = date('Y-m-d H:i:s'); // 購買時間
$status = '待付款'; // 預設狀態

$servername = "DESKTOP-0G56S7G\\SQLEXPRESS";
$dbname = "final";
$dbusername = "sa";
$dbpassword = "StrongPass123!";

try {
    $conn = new PDO("sqlsrv:Server=$servername;Database=$dbname", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 取得購物車商品資料（包含價格）
    $cartQuery = $conn->prepare("SELECT c.product_id, c.quantity, p.price 
                                 FROM cart c
                                 JOIN products p ON c.product_id = p.id
                                 WHERE c.customer_id = ?");
    $cartQuery->execute([$customer_id]);
    $cartItems = $cartQuery->fetchAll(PDO::FETCH_ASSOC);

    if (count($cartItems) === 0) {
        echo "購物車是空的，無法結帳。";
        exit;
    }

    $total = 0;

    // 將每筆商品插入 orders 表
    $insertOrder = $conn->prepare("INSERT INTO orders (customer_id, product_id, quantity, price, order_time, status)
                                   VALUES (?, ?, ?, ?, ?, ?)");

    foreach ($cartItems as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;

        $insertOrder->execute([
            $customer_id,
            $item['product_id'],
            $item['quantity'],
            $item['price'],
            $purchase_time,
            $status
        ]);
    }

    // 更新 customer 的 total_spent
    $update = $conn->prepare("UPDATE customer SET total_spent = ISNULL(total_spent, 0) + ? WHERE id = ?");
    $update->execute([$total, $customer_id]);

    // 清空購物車
    $clearCart = $conn->prepare("DELETE FROM cart WHERE customer_id = ?");
    $clearCart->execute([$customer_id]);

    // 導向 order.php
    echo "<script>alert('購買成功！總共花費 $total 元，訂單已建立'); location.href='order.php';</script>";

} catch (PDOException $e) {
    echo "錯誤：" . $e->getMessage();
}
?>
