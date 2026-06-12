<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$orders_query = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY order_date DESC";
$orders_result = mysqli_query($connection, $orders_query);

include 'header.php';
?>

<h2 class="mb-3">Мои заказы</h2>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Заказ успешно оформлен! Администратор свяжется с вами.</div>
<?php endif; ?>

<?php if (mysqli_num_rows($orders_result) > 0): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>№ заказа</th>
                    <th>Дата</th>
                    <th>Получатель</th>
                    <th>Город</th>
                    <th>Стоимость</th>
                    <th>Статус</th>
                    <th>Детали</th>
                </tr>
            </thead>
            <tbody>
                <?php while($order = mysqli_fetch_assoc($orders_result)): ?>
                    <tr>
                        <td><?php echo $order['id']; ?></td>
                        <td><?php echo date('d.m.Y H:i', strtotime($order['order_date'])); ?></td>
                        <td><?php echo htmlspecialchars($order['recipient_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['delivery_city']); ?></td>
                        <td><strong><?php echo number_format($order['total_cost'], 2); ?> руб.</strong></td>
                        <td>
                            <?php
                            $status_class = match($order['status']) {
                                'обработка' => 'bg-secondary',
                                'оформлен' => 'bg-primary',
                                'передается перевозчику' => 'bg-info',
                                'доставка' => 'bg-warning',
                                'доставлен' => 'bg-success',
                                'закончен' => 'bg-dark',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?php echo $status_class; ?>"><?php echo $order['status']; ?></span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#details<?php echo $order['id']; ?>">
                                Показать состав
                            </button>
                        </td>
                    </tr>
                    <tr class="collapse" id="details<?php echo $order['id']; ?>">
                        <td colspan="7">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Товар</th>
                                        <th>Количество (кг)</th>
                                        <th>Цена за кг (руб)</th>
                                        <th>Сумма (руб)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $items_query = "
                                        SELECT oi.*, p.name as product_name
                                        FROM order_items oi
                                        JOIN products p ON oi.product_id = p.id
                                        WHERE oi.order_id = " . $order['id'];
                                    $items_result = mysqli_query($connection, $items_query);
                                    while($item = mysqli_fetch_assoc($items_result)):
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                        <td><?php echo number_format($item['quantity_kg'], 2); ?> кг</td>
                                        <td><?php echo number_format($item['price_per_kg'], 2); ?></td>
                                        <td><?php echo number_format($item['subtotal'], 2); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                            <div class="mt-2">
                                <strong>Адрес доставки:</strong> <?php echo htmlspecialchars($order['delivery_address']); ?>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        У вас пока нет заказов. <a href="catalog.php">Перейти в каталог</a>
    </div>
<?php endif; ?>

<?php include 'footer.php'; ?>