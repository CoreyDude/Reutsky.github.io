<?php
// проверка авторизации
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// перенаправление
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

// получение роли входящего
function getUserRole() {
    return $_SESSION['user_role'] ?? 'guest';
}

// проверка на администратора
function isAdmin() {
    return getUserRole() === 'admin';
}

// получение списка товаров
function getAllProducts($connection) {
    $query = "SELECT * FROM products ORDER BY name";
    $result = mysqli_query($connection, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// получение товара по ID
function getProductById($connection, $id) {
    $query = "SELECT * FROM products WHERE id = $id";
    $result = mysqli_query($connection, $query);
    return mysqli_fetch_assoc($result);
}

// картинки для товаров по названию
function getProductImageUrl($productName) {
    // Сопоставление названий товаров с качественными изображениями с Unsplash
    $images = [
	    'Горох' => 'https://www.altaiagro.com/upload/information_system_35/4/7/3/item_473/item_473.jpg',
		'Кукуруза' => 'https://agronigeria.ng/wp-content/uploads/2023/10/Maize-Farming.jpg',
	    'Овес' => 'https://forumfermer.ru/wp-content/uploads/1/d/c/1dcb87dd3f0e0e6472007184be607758.jpeg',
        'Пшеница' => 'https://media.istockphoto.com/id/155353267/photo/stalks-of-ripe-grain-in-fielsd.jpg?s=170667a&w=0&k=20&c=6J4EGJVD4iwqHcdaETs869-ClE3vYREL72UfkezIHsY=',
        'Рапс' => 'https://th.bing.com/th/id/R.6f4608806da2c1c0de9e9f6b5142c3c9?rik=%2fRzNQIuTSFpdeQ&pid=ImgRaw&r=0',
		'Рожь' => 'https://catalog.semenaopt.ru/uploads/product/38600/38649/c9b6e05055ea11eea1fba8a159914cc0_eef62b7d561d11eea1fba8a159914cc0.jpg',
		'Соя' => 'https://baibolsyn.kz/media/images/Soya_sort_ES_Mentor.2e16d0ba.fill-490x305-c100.jpg',
        'Ячмень' => 'https://th.bing.com/th/id/R.4959f6bec958418e2d419913277e1bbe?rik=hy2OAiUijcNmDw&riu=http%3a%2f%2fwww.viterra.com%2f.imaging%2fmte%2fviterra%2fxl%2fdam%2fviterra-com%2findex%2fBARLEY_01_140303-2.jpg%2fjcr%3acontent%2fBARLEY_01_140303-2.jpg&ehk=dWDyWBtosYFpxHSnSh7zFz%2bXKdCQ7Fx7rrEdgJp55zs%3d&risl=&pid=ImgRaw&r=0',
        
    ];
    
    // Ищем совпадение в названии
    foreach ($images as $key => $url) {
        if (strpos($productName, $key) !== false) {
            return $url;
        }
    }
    
    // Изображение по умолчанию (пшеничное поле)
    return 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=400&h=300&fit=crop';
}
?>