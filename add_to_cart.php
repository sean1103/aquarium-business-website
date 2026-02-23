<?php
session_start();

if (!isset($_SESSION["customer_id"])) {
    echo "請先登入";
    exit;
}

// 資料庫設定
$servername = "DESKTOP-0G56S7G\\SQLEXPRESS";
$dbname = "final";
$dbusername = "sa";
$dbpassword = "StrongPass123!";

try {
    // 使用 PDO 連線 SQL Server
    $conn = new PDO("sqlsrv:Server=$servername;Database=$dbname", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $customer_id = $_SESSION["customer_id"];
        $product_name = $_POST["product_name"];
        $quantity = 1;

        // 取得商品 ID
        $stmt = $conn->prepare("SELECT id, quantity FROM products WHERE name = :product_name");
        $stmt->bindParam(':product_name', $product_name);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $product_id = $product["id"];
            $stock_quantity = $product["quantity"];

            if ($stock_quantity > 0) {
                // 查詢購物車中是否已有這筆商品
                $checkStmt = $conn->prepare("SELECT cart_id, quantity FROM cart WHERE customer_id = :customer_id AND product_id = :product_id");
                $checkStmt->bindParam(':customer_id', $customer_id);
                $checkStmt->bindParam(':product_id', $product_id);
                $checkStmt->execute();
                $cartItem = $checkStmt->fetch(PDO::FETCH_ASSOC);

                if ($cartItem !== false) {
                    // 商品已存在 → 更新數量
                    $new_qty = $cartItem["quantity"] + 1;
                    $updateStmt = $conn->prepare("UPDATE cart SET quantity = :quantity WHERE cart_id = :cart_id");
                    $updateStmt->bindParam(':quantity', $new_qty);
                    $updateStmt->bindParam(':cart_id', $cartItem["cart_id"]);
                    $updateStmt->execute();
                } else {
                    // 商品不存在 → 新增進購物車
                    $insertStmt = $conn->prepare("INSERT INTO cart (customer_id, product_id, quantity, added_at) VALUES (:customer_id, :product_id, 1, GETDATE())");
                    $insertStmt->bindParam(':customer_id', $customer_id);
                    $insertStmt->bindParam(':product_id', $product_id);
                    $insertStmt->execute();
                }

                // 更新商品庫存數量，減少 1
                $new_stock = $stock_quantity - 1;
                if ($new_stock >= 0) {
                    $updateStockStmt = $conn->prepare("UPDATE products SET quantity = :quantity WHERE id = :product_id");
                    $updateStockStmt->bindParam(':quantity', $new_stock);
                    $updateStockStmt->bindParam(':product_id', $product_id);
                    $updateStockStmt->execute();
                    
                    echo " $product_name 已成功加入購物車🛒!! 庫存剩餘: $new_stock 件";
                } else {
                    echo "商品庫存不足，無法加入購物車。";
                }
            } else {
                echo "商品庫存不足，無法加入購物車。";
            }

        } else {
            echo "找不到商品";
        }
    }

} catch (PDOException $e) {
    echo "資料庫錯誤：" . $e->getMessage();
}
?>
