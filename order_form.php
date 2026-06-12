<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$products = getAllProducts($connection);
$selected_product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : null;

include 'header.php';
?>

<h2 class="mb-3">Оформление заказа</h2>

<div class="row">
    <div class="col-md-7">
        <form method="POST" action="place_order.php" id="orderForm">
            <h4>1. Выберите товары</h4>
            <p class="text-muted">Минимальный заказ на позицию — <strong>10 кг</strong>.</p>
            
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Товар</th>
                            <th>Цена (руб/кг)</th>
                            <th>В наличии (кг)</th>
                            <th>Количество (кг)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $product): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($product['name']); ?></strong><br>
                                <small class="text-muted"></small>
                            </td>
                            <td class="price-cell" data-price="<?php echo $product['price_per_kg']; ?>">
                                <?php echo number_format($product['price_per_kg'], 2); ?>
                            </td>
                            <td class="stock-cell" data-stock="<?php echo $product['in_stock_kg']; ?>">
                                <?php echo number_format($product['in_stock_kg'], 0); ?> кг
                            </td>
                            <td>
                                <input type="number" 
                                       name="quantity[<?php echo $product['id']; ?>]" 
                                       class="form-control quantity-input" 
                                       min="0" 
                                       step="1"
                                       value="0"
                                       data-price="<?php echo $product['price_per_kg']; ?>"
                                       data-product-id="<?php echo $product['id']; ?>"
                                       data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                                       data-stock="<?php echo $product['in_stock_kg']; ?>">
                                <!-- <small class="text-muted">0 = не заказывать</small> -->
                                <div class="invalid-feedback" style="display: none;"></div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <h4>2. Информация о получателе и доставке</h4>
            <div class="mb-3">
                <label for="recipient_name" class="form-label">ФИО получателя *</label>
                <input type="text" class="form-control" name="recipient_name" required>
            </div>
            <div class="mb-3">
                <label for="delivery_city" class="form-label">Город доставки *</label>
                <input type="text" class="form-control" name="delivery_city" required>
            </div>
            <div class="mb-3">
                <label for="delivery_address" class="form-label">Адрес доставки *</label>
                <textarea class="form-control" name="delivery_address" rows="3" required></textarea>
            </div>
            
            <h4>3. Итого</h4>
            <div class="mb-3">
                <strong>Общая стоимость: </strong><span id="totalCostDisplay">0.00</span> руб.
                <input type="hidden" name="total_cost" id="totalCostInput" value="0">
            </div>
            
            <button type="submit" class="btn btn-primary" id="submitBtn">Оформить заказ</button>
			<br><br>
        </form>
    </div>
    
    <div class="col-md-5">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Правила оформления заказа:</h5>
                <ul>
                    <li>Минимальный заказ на одну позицию — <strong>10 кг</strong></li>
                    <li>Если количество = 0, товар не включается в заказ</li>
                    <li>Можно заказать от 1 до нескольких видов зерна</li>
                    <li>Цены указаны за 1 кг</li>
                    <li>После оформления заказа статус будет "обработка"</li>
                </ul>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Ваш заказ</h5>
                <div id="orderSummary">
                    <p class="text-muted">Выберите товары и укажите количество</p>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <strong>Итого:</strong>
                    <strong id="summaryTotal">0.00 руб.</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInputs = document.querySelectorAll('.quantity-input');
    const totalCostDisplay = document.getElementById('totalCostDisplay');
    const totalCostInput = document.getElementById('totalCostInput');
    const orderSummary = document.getElementById('orderSummary');
    const summaryTotal = document.getElementById('summaryTotal');
    const submitBtn = document.getElementById('submitBtn');
    
    function validateQuantity(input) {
        const quantity = parseFloat(input.value);
        const stock = parseFloat(input.dataset.stock);
        const productName = input.dataset.productName;
        const feedbackDiv = input.parentElement.querySelector('.invalid-feedback');
        
        // Если 0 - пропускаем валидацию
        if (quantity === 0) {
            input.classList.remove('is-invalid');
            if (feedbackDiv) feedbackDiv.style.display = 'none';
            return true;
        }
        
        // Проверка на минимальное количество
        if (quantity < 10) {
            input.classList.add('is-invalid');
            if (feedbackDiv) {
                feedbackDiv.textContent = `Минимальный заказ - 10 кг (сейчас ${quantity} кг)`;
                feedbackDiv.style.display = 'block';
            }
            return false;
        }
        
        // Проверка на наличие на складе
        if (quantity > stock) {
            input.classList.add('is-invalid');
            if (feedbackDiv) {
                feedbackDiv.textContent = `Недостаточно товара. Доступно: ${stock} кг`;
                feedbackDiv.style.display = 'block';
            }
            return false;
        }
        
        input.classList.remove('is-invalid');
        if (feedbackDiv) feedbackDiv.style.display = 'none';
        return true;
    }
    
    function updateOrderSummary() {
        let total = 0;
        const items = [];
        
        quantityInputs.forEach(input => {
            const quantity = parseFloat(input.value) || 0;
            const price = parseFloat(input.dataset.price);
            const productName = input.dataset.productName;
            const isValid = validateQuantity(input);
            
            if (quantity > 0 && isValid) {
                const subtotal = quantity * price;
                total += subtotal;
                items.push({
                    name: productName,
                    quantity: quantity,
                    price: price,
                    subtotal: subtotal
                });
            }
        });
        
        // Обновляем отображение сводки
        if (items.length > 0) {
            let html = '<div class="table-responsive"><table class="table table-sm">';
            html += '<thead><tr><th>Товар</th><th>Кол-во (кг)</th><th>Сумма</th></tr></thead><tbody>';
            items.forEach(item => {
                html += `<tr>
                            <td>${item.name}</td>
                            <td>${item.quantity.toFixed(2)} кг</td>
                            <td>${item.subtotal.toFixed(2)} руб.</td>
                         </tr>`;
            });
            html += '</tbody></table></div>';
            orderSummary.innerHTML = html;
        } else {
            orderSummary.innerHTML = '<p class="text-muted">Выберите товары и укажите количество</p>';
        }
        
        summaryTotal.textContent = total.toFixed(2) + ' руб.';
        totalCostDisplay.textContent = total.toFixed(2);
        totalCostInput.value = total.toFixed(2);
        
        // Блокируем кнопку отправки, если нет товаров
        const hasValidItems = items.length > 0;
        submitBtn.disabled = !hasValidItems;
        if (!hasValidItems) {
            submitBtn.title = 'Добавьте хотя бы один товар (от 10 кг)';
        } else {
            submitBtn.title = '';
        }
    }
    
    quantityInputs.forEach(input => {
        input.addEventListener('input', function() {
            // Если значение не 0 и меньше 10 - можно оставить, но подсветим ошибку
            validateQuantity(this);
            updateOrderSummary();
        });
        
        input.addEventListener('blur', function() {
            let value = parseFloat(this.value);
            // Если значение между 1 и 9 - можно предупредить, но не исправлять автоматически
            if (value > 0 && value < 10) {
                // Не исправляем автоматически, просто показываем ошибку
                validateQuantity(this);
            }
        });
    });
    
    updateOrderSummary();
});
</script>

<style>
.quantity-input.is-invalid {
    border-color: #dc3545;
    background-color: #fff0f0;
}
.invalid-feedback {
    font-size: 0.75rem;
    margin-top: 0.25rem;
}
</style>

<?php include 'footer.php'; ?>