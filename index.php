<?php
require_once 'config.php';
require_once 'functions.php';

include 'header.php';
?>

<div class="row">
    <div class="col-md-8">
        <div class="card p-4 mb-4">
            <h2>О компании</h2>
            <p>Общество с ограниченной ответственностью «Сибирь» является организацией, которая покупает и продает зерновые культуры, расположенный по адресу: Новосибирская область, Искитимский район, р.п.Линево, бульвар Ветеранов Войны дом 30.</p>
            <p>Является частной собственностью, дата регистрации 05.02.2019г. с уставным капиталом 10 000 рублей.</p>
            <p>Основной вид деятельности по ОКВЭД: торговля оптовая зерном, необработанным табаком, семенами и кормами для сельскохозяйственных животных.</p>
        </div>
        
        <h3 class="mt-4 mb-3">Наша продукция</h3>
        <div class="row">
            <?php
            $products = getAllProducts($connection);
            $counter = 0;
            foreach($products as $product):
                if($counter >= 4) break; // показываем только 4 товара на главной
                $productImage = getProductImageUrl($product['name']);
                $counter++;
            ?>
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="<?php echo $productImage; ?>" class="img-fluid rounded-start" style="height: 100%; object-fit: cover;" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                                <p class="card-text"><strong>от <?php echo number_format($product['price_per_kg'], 0); ?> руб/кг</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-3">
            <a href="catalog.php" class="btn btn-outline-success">Смотреть весь каталог</a>
        </div>
		<br>
    </div>
    
    <div class="col-md-4">
        <div class="card contact-card">
            <div class="card-body">
                <h5 class="card-title">Контакты</h5>
                <p><strong>Адрес:</strong><br>Новосибирская область,<br>Искитимский район, р.п.Линево,<br>бульвар Ветеранов Войны дом 30</p>
                <hr>
                <p><strong>Телефон:</strong><br>+7 (383) 123-45-67</p>
                <p><strong>Email:</strong><br>siberia@agro.ru</p>
                <hr>
                <p><strong>Режим работы:</strong><br>Пн-Пт: 9:00 - 18:00</p>
                <p><strong>Минимальный заказ:</strong><br>от 10 кг (оптовая продажа)</p>
            </div>
        </div>
        
        <!-- Дополнительный блок с преимуществами -->
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Наши преимущества</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">✓ Качественное зерно от производителей Сибири</li>
                    <li class="mb-2">✓ Удобная система для оформления и отслеживания заказа</li>
                    <li class="mb-2">✓ Доставка по всей России</li>
                    <li class="mb-2">✓ Сертификаты качества на всю продукцию</li>
                </ul>
            </div>
        </div>
		<br>
    </div>
</div>

<?php include 'footer.php'; ?>