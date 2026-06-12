<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$recipient_name = mysqli_real_escape_string($connection, $_POST['recipient_name']);
$delivery_city = mysqli_real_escape_string($connection, $_POST['delivery_city']);
$delivery_address = mysqli_real_escape_string($connection, $_POST['delivery_address']);
$total_cost = floatval($_POST['total_cost']);
$quantities = $_POST['quantity'];

mysqli_begin_transaction($connection);

try {
    // Фильтруем только те товары, где количество > 0
    $ordered_items = [];
    foreach ($quantities as $product_id => $quantity) {
        $quantity = floatval($quantity);
        if ($quantity > 0) {
            // Проверяем минимальное количество
            if ($quantity < 10) {
                $product = getProductById($connection, $product_id);
                throw new Exception("Товар '{$product['name']}': минимальный заказ 10 кг (вы указали {$quantity} кг)");
            }
            $ordered_items[$product_id] = $quantity;
        }
    }
    
    if (empty($ordered_items)) {
        throw new Exception('Добавьте хотя бы один товар. Минимальный заказ на позицию - 10 кг');
    }
    
    // Создаём заказ
    $order_date = date('Y-m-d H:i:s');
    $order_query = "INSERT INTO orders (user_id, order_date, total_cost, status, recipient_name, delivery_city, delivery_address) 
                    VALUES ($user_id, '$order_date', $total_cost, 'обработка', '$recipient_name', '$delivery_city', '$delivery_address')";
    mysqli_query($connection, $order_query);
    $order_id = mysqli_insert_id($connection);
    
    $errors = [];
    
    // Добавляем позиции заказа
    foreach ($ordered_items as $product_id => $quantity) {
        $product = getProductById($connection, $product_id);
        if (!$product) {
            $errors[] = "Товар не найден";
            continue;
        }
        
        $price_per_kg = $product['price_per_kg'];
        $subtotal = $quantity * $price_per_kg;
        
        // Проверяем остаток на складе
        if ($product['in_stock_kg'] < $quantity) {
            $errors[] = "Недостаточно товара '{$product['name']}'. Доступно: {$product['in_stock_kg']} кг, запрошено: {$quantity} кг";
            continue;
        }
        
        $item_query = "INSERT INTO order_items (order_id, product_id, quantity_kg, price_per_kg, subtotal) 
                       VALUES ($order_id, $product_id, $quantity, $price_per_kg, $subtotal)";
        mysqli_query($connection, $item_query);
        
        // Обновляем склад
        $update_stock = "UPDATE products SET in_stock_kg = in_stock_kg - $quantity WHERE id = $product_id";
        mysqli_query($connection, $update_stock);
    }
    
    if (!empty($errors)) {
        throw new Exception(implode("<br>", $errors));
    }
    
    mysqli_commit($connection);
    redirect("my_orders.php?success=1");
    
} catch (Exception $e) {
    mysqli_rollback($connection);
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ошибка при оформлении заказа</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h4 class="mb-0">Ошибка при оформлении заказа</h4>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-danger">
                                <?php echo $e->getMessage(); ?>
                            </div>
                            <p class="mt-3">Пожалуйста, исправьте ошибки и попробуйте снова:</p>
                            <ul>
                                <li>Каждый заказываемый товар должен быть от 10 кг</li>
                                <li>Нельзя заказывать больше, чем есть на складе</li>
                                <li>Можно заказать несколько разных товаров</li>
                            </ul>
                            <div class="mt-4">
                                <a href="order_form.php" class="btn btn-primary">Вернуться к форме заказа</a>
                                <a href="catalog.php" class="btn btn-secondary">В каталог</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>