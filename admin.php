<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

// Обновление статуса заказа
if (isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = mysqli_real_escape_string($connection, $_POST['status']);
    $update = "UPDATE orders SET status = '$status' WHERE id = $order_id";
    mysqli_query($connection, $update);
}

$orders_query = "
    SELECT o.*, u.username 
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.order_date DESC";
$orders_result = mysqli_query($connection, $orders_query);

include 'header.php';
?>

<h2 class="mb-3">Управление заказами</h2>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>№ заказа</th>
                <th>Кто оформил</th>
                <th>Дата</th>
                <th>Получатель</th>
                <th>Город</th>
                <th>Стоимость</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php while($order = mysqli_fetch_assoc($orders_result)): ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo htmlspecialchars($order['username']); ?></td>
                    <td><?php echo date('d.m.Y H:i', strtotime($order['order_date'])); ?></td>
                    <td><?php echo htmlspecialchars($order['recipient_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['delivery_city']); ?></td>
                    <td><strong><?php echo number_format($order['total_cost'], 2); ?> руб.</strong></td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <select name="status" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                                <option value="обработка" <?php echo $order['status'] == 'обработка' ? 'selected' : ''; ?>>Обработка</option>
                                <option value="оформлен" <?php echo $order['status'] == 'оформлен' ? 'selected' : ''; ?>>Оформлен</option>
                                <option value="передается перевозчику" <?php echo $order['status'] == 'передается перевозчику' ? 'selected' : ''; ?>>Передается перевозчику</option>
                                <option value="доставка" <?php echo $order['status'] == 'доставка' ? 'selected' : ''; ?>>Доставка</option>
                                <option value="доставлен" <?php echo $order['status'] == 'доставлен' ? 'selected' : ''; ?>>Доставлен</option>
                                <option value="закончен" <?php echo $order['status'] == 'закончен' ? 'selected' : ''; ?>>Закончен</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-sm btn-success">Обновить</button>
                        </form>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#adminDetails<?php echo $order['id']; ?>">
                            Состав
                        </button>
                    </td>
                </tr>
                <tr class="collapse" id="adminDetails<?php echo $order['id']; ?>">
                    <td colspan="8">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr><th>Товар</th><th>Кол-во (кг)</th><th>Цена/кг</th><th>Сумма</th></tr>
                            </thead>
                            <tbody>
                                <?php
                                $items_query = "
                                    SELECT oi.*, p.name 
                                    FROM order_items oi
                                    JOIN products p ON oi.product_id = p.id
                                    WHERE oi.order_id = " . $order['id'];
                                $items_result = mysqli_query($connection, $items_query);
                                while($item = mysqli_fetch_assoc($items_result)):
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td><?php echo number_format($item['quantity_kg'], 2); ?> кг</td>
                                    <td><?php echo number_format($item['price_per_kg'], 2); ?></td>
                                    <td><?php echo number_format($item['subtotal'], 2); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <div><strong>Адрес:</strong> <?php echo htmlspecialchars($order['delivery_address']); ?></div>
                        <div><strong>Получатель:</strong> <?php echo htmlspecialchars($order['recipient_name']); ?></div>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>