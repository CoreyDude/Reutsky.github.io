<?php
require_once 'config.php';
require_once 'functions.php';

include 'header.php';
?>

<h2 class="mb-3">Каталог зерновых культур</h2>
<p class="text-muted mb-4">Минимальный заказ — от 10 кг по каждой позиции.</p>

<div class="row">
    <?php
    $products = getAllProducts($connection);
    foreach($products as $product):
        $productImage = getProductImageUrl($product['name']);
    ?>
    <div class="col-md-4 mb-4">
        <div class="card h-100 product-card">
            <img src="<?php echo $productImage; ?>" class="card-img-top product-img" alt="<?php echo htmlspecialchars($product['name']); ?>">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                <p class="card-text"><strong>Цена: <?php echo number_format($product['price_per_kg'], 2); ?> руб/кг</strong></p>
                <p class="card-text text-muted">В наличии: <?php echo number_format($product['in_stock_kg'], 0); ?> кг</p>
                <?php if (isLoggedIn()): ?>
                    <a href="order_form.php?product_id=<?php echo $product['id']; ?>" class="btn btn-primary w-100">Заказать</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-secondary w-100">Войдите для заказа</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php include 'footer.php'; ?>