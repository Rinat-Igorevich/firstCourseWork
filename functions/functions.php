<?php

namespace functions;

use PDO;

function showMenu($menuClass)
{
    include $_SERVER['DOCUMENT_ROOT'] . '/template/menu.php';
}

function getConnection()
{
    static $connect = null;

    if (null === $connect)
    {
        $connect = mysqli_connect(
            DB_CONNECTION_HOST,
            DB_CONNECTION_USER_NAME,
            DB_CONNECTION_PASSWORD,
            DB_CONNECTION_DB_NAME)
        or die('Connection error');
    }
    return $connect;
}

function getPDO()
{
    $dsn = 'mysql:host=' . DB_CONNECTION_HOST . '; dbname=' . DB_CONNECTION_DB_NAME;

    return new PDO(
        $dsn,
        DB_CONNECTION_USER_NAME,
        DB_CONNECTION_PASSWORD
    );
}

function verifyUser($login, $password): bool
{
    $user = getUser($login);

    if ($user != null && password_verify($password, $user['password']))
    {
        return true;
    }
    return false;
}

function getUser($email)
{
    $pdo = getPDO();
    $statement = $pdo->prepare("SELECT * FROM users WHERE email= ? ");
    $statement->execute([$email]);
    $result = $statement->fetch( PDO::FETCH_ASSOC);
    $pdo = null;
    return $result;
}

function getMenu($menu, string $role = null): array
{
    if ($role == 'admin') {

    } elseif ($role == 'operator') {
        unset($menu[array_search('admin', array_column($menu, 'role'))]);

    } else {
        unset($menu[array_search('admin', array_column($menu, 'role'))]);
        $menu = array_values($menu);
        unset($menu[array_search('operator', array_column($menu, 'role'))]);

    }
    return $menu;
}

function getProducts(): array
{
    $sorting = getSort();
    return getProductsWithFilter($sorting);
}

function getSort()
{
    $path = explode('/', parse_url($_SERVER['REQUEST_URI'])['path']);

    $query = $_GET;

    if (isset($path[3]) && $path[3] != 'products') {
        $query['category'] = $path[3];
    }

    return $query;
}

function getProductsWithFilter($params)
{
    $products = [];
    $connection = getConnection();
    $query = getQuery($params);
    $limit = PRODUCTS_ON_PAGE;

    if (!isset($params['page'])) {
        $params['page'] = 1;
    }

    $offset = ($params['page'] - 1) * $limit;

    $query .= " LIMIT $offset, $limit";

    $result = mysqli_query($connection, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }

    return $products;
}

function getProductsCount($params)
{
    $connection = getConnection();
    $query = getQuery($params);

    $result = mysqli_query($connection, $query);
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    return count($products);
}

function getPriceForSlider()
{
    $pdo = getPDO();
    $statement = $pdo->prepare("SELECT MAX(price) FROM products");
    $statement->execute();
    $result['maximumPrice'] = floatval($statement->fetchColumn());
    $statement = $pdo->prepare("SELECT MIN(price) FROM products");
    $statement->execute();
    $result['minimumPrice'] = floatval($statement->fetchColumn());
    $pdo = null;
    return $result;
}

function getQuery($params)
{
    $query = "SELECT product.* FROM products AS product 
                LEFT JOIN category_product cp ON product.id = cp.product_id
                LEFT JOIN categories c ON c.id = cp.category_id
              WHERE is_active = TRUE ";

    if (isset($params['new']) && $params['new'] == 'on'){
        $isNew = TRUE;
    }

    if (isset($params['sale']) && $params['sale'] == 'on'){
        $isSale = TRUE;
    }

    if (isset($params['category']) && !empty($params['category'])) {
        $category = $params['category'];
        $query .= " AND c.name = '$category'";
    }

    if (isset($params['category']) && !empty($params['category'])) {
        $category = $params['category'];
        $query .= " AND c.name = '$category'";
    }

    if (isset($params['min'], $params['max']) && !empty($params['min']) && !empty($params['max'])) {
        $query .= " AND price BETWEEN {$params['min']} AND {$params['max']}";
    }
    if (isset($isNew) && $isNew) {
        $query .= " AND is_new = TRUE";
    }
    if (isset($isSale) && $isSale) {
        $query .= " AND is_sale = TRUE";
    }
    $query .= " group by product.id ";

    if (isset($params['sortBy']) && !empty($params['sortBy'])) {
        $query .= ',' . $params['sortBy']. ' ORDER BY ' . $params['sortBy'];
    }
    if (isset($params['sortOrder']) && !empty($params['sortOrder'])) {
        $query .= " {$params['sortOrder']}";
    }

    return $query;
}

function getProductsByAjax()
{
    $pdo = getPDO();
    $data = [];

    $data['query'] = getQuery($_POST);
    $statement = $pdo->prepare($data['query']);
    $statement->execute();

    $data['productsCount'] = $statement->rowCount();

    $offset = ($_POST['page'] - 1) * PRODUCTS_ON_PAGE;

    $data['query'] .= " LIMIT $offset, " . PRODUCTS_ON_PAGE;

    $statement = $pdo->prepare($data['query']);
    $statement->execute();
    $result = $statement->fetchall();


    if ($data['productsCount'] > 0) {
        if ($_POST['requestFrom'] == 'shop') {
            $data['output'] = drawProductsForShop($result);
        }
    }

    $data['pagination'] = getPaginatorForAjax();

    return $data;
}

function drawProductsForShop($fetchResult){

    $output = '<section class="shop__list">';
    foreach ($fetchResult as $row) {
        $output .= '
                <article class="shop__item product" id="' . $row['id'] . '" tabindex="0">
                    <div class="product__image">
                        <img src="' . $row['src'] . '" alt="product-name">
                    </div>
                    <p class="product__name">' . $row['name'] . '</p>
                    <span class="product__price">' . number_format($row['price'], 0, ' . ', ' ') . ' руб.</span>
                </article>';
    }
    $output .= '</section>';

    return $output;
}

function getPaginator($arr): array
{
    $paginator = [];
    $path = explode('/', parse_url($_SERVER['REQUEST_URI'])['path']);
    $category = '';

    if (isset($path[3]) && $path[3] != 'products'){
        $category = $path[3];
    }

    $arr['category'] = $category;
    $countOfProducts = getProductsCount($arr);

    $paginator['currentPage'] = $_GET['page'] ?? 1;

    $paginator['pagesCount'] = ceil($countOfProducts / PRODUCTS_ON_PAGE);
    if (!($paginator['currentPage'] == 0)) {
        $paginator['prevPage'] = $paginator['currentPage'] - 1;
    } else {
        $paginator['prevPage'] = 0;
    }

    $paginator['nextPage'] = $paginator['currentPage'] + 1;

    return $paginator;
}

function getPaginatorForAjax()
{
    $paginator = [];
    $countOfProducts = getProductsCount($_POST);

    $paginator['currentPage'] = $_POST['page'] ?? 1;

    $paginator['pagesCount'] = ceil($countOfProducts / PRODUCTS_ON_PAGE);
    if (!($paginator['currentPage'] == 0)) {
        $paginator['prevPage'] = $paginator['currentPage'] - 1;
    } else {
        $paginator['prevPage'] = 0;
    }
    $paginator['nextPage'] = $paginator['currentPage'] + 1;

    $output = '';
    if (!($paginator['prevPage'] == 0)) {
        $output .= '<li>
                       <a class="paginator__item"><</a>
                    </li>';
    }
    for ($i = 1; $i <= $paginator['pagesCount']; $i++) {
        if ($paginator['currentPage'] == $i) {
            $output .= '<li>
                            <a class="paginator__item active">' . $i . '</a>
                        </li>';
        } else {
            $output .= '<li>
                            <a class="paginator__item" >' . $i . '</a>
                        </li>';
        }
    }
    if (!($paginator['nextPage'] > $paginator['pagesCount'])) {
        $output .= '<li>
                        <a class="paginator__item">></a>
                    </li>';
    }

    return $output;
}

function validateData()
{
    $error = null;
    if (strlen(trim($_POST['surname'])) == 0) {
        $error .= 'Поле ФАМИЛИЯ заполнено неверно; ';
    }
    if (strlen(trim($_POST['name'])) == 0) {
        $error .= 'Поле ИМЯ заполнено неверно; ';
    }

    if (strlen(trim($_POST['phone'])) != 10) {
        $error .= "Поле 'Телефон' заполнено неверно (необходимо ввести 10 цифр без пробелов и без '8';";
    }
    if (strlen(trim($_POST['customerEmail'])) == 0) {
        $error .= "Поле 'Почта' заполнено неверно; ";
    }
    if ($_POST['delivery'] == 'courier') {
        if (strlen(trim($_POST['city'])) == 0) {
            $error .= "Поле 'Город' заполнено неверно; ";
        }
        if (strlen(trim($_POST['street'])) == 0) {
            $error .= "Поле 'Улица' заполнено неверно; ";
        }
        if (strlen(trim($_POST['home'])) == 0) {
            $error .= "Поле 'Дом' заполнено неверно; ";
        }
        if (strlen(trim($_POST['aprt'])) == 0) {
            $error .= "Поле 'Квартира' заполнено неверно; ";
        }
    }

    return $error;
}

function createOrder()
{
    $connection = getPDO();

    $amount = getProductCost($_POST['productID']);
    $address = PICKUP_ADDRESS;
    if ($_POST['delivery'] == 'courier') {
        $amount += EXTRA_PRICE_FOR_DELIVERY;
        $address = 'г.'   . $_POST['city'] .
                   ' ул.' . $_POST['street'] .
                   ' д.'  . $_POST['home'] .
                   ' кв.' . $_POST['aprt'];
    }

    if (!isCustomerExist($_POST['customerEmail'])) {
        createCustomer();
    }
    $customerID = getCustomerID($_POST['customerEmail']);

    $query = 'INSERT INTO orders(customer_id,
                                 delivery_method, 
                                 payment_method, 
                                 comment, 
                                 address, 
                                 amount)
                    VALUES (?, ?, ?, ?, ?, ?)';

    $stmt = $connection->prepare($query);
    $stmt->execute(array($customerID,
                         $_POST['delivery'],
                         $_POST['payment'],
                         $_POST['comment'],
                         $address,
                         $amount));
    return $query;
}

function getProductCost($id)
{
    $connection = getPDO();
    $stmt = $connection->prepare('SELECT price FROM products WHERE id =' . intval($id));
    $stmt->execute();
    if ($row = $stmt->fetch()) {
        return $row['price'];
    }
}

function getProductByID($id)
{
    $connection = getPDO();

    $stmt = $connection->prepare(
        'SELECT products.*, c.name AS category  FROM products
                LEFT JOIN category_product cp ON products.id = cp.product_id
                LEFT JOIN categories c ON c.id = cp.category_id
               WHERE products.id =' . intval($id));
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $result = $row;
        $category[] = $row['category'];
    }

    $result['category'] = $category;
    return $result;

}

function isCustomerExist($email)
{
    $connection = getPDO();
    $query = 'SELECT id FROM customers WHERE email = ?';
    $statement = $connection->prepare($query);
    $statement->execute(array($email));
    if ($statement->rowCount() > 0) {
        return true;
    }
    return false;
}

function createCustomer()
{
    $connection = getPDO();

    $query = 'INSERT INTO customers (surname, name, third_name, phone, email)
                     VALUES (?, ?, ?, ?, ?)';
    $statement = $connection->prepare($query);
    $statement->execute(array(
                            $_POST['surname'],
                            $_POST['name'],
                            ($_POST['thirdName'] ?? 'null'),
                            $_POST['phone'],
                            $_POST['customerEmail']
                            )
                        );
}

function getCustomerID($email)
{
    $connection = getPDO();

    $query = 'SELECT id FROM customers WHERE email = ?';

    $statement = $connection->prepare($query);
    $statement->execute(array($email));

    if ($row = $statement->fetch()) {
        return $row['id'];
    }
}

function getOrders()
{
    $pdo = getPDO();
    $query = 'SELECT orders.*, CONCAT(c.surname, " ", c.name, " ", c.third_name) AS fullName, c.phone FROM orders
                LEFT JOIN customers c ON c.id = orders.customer_id ORDER BY is_done, date DESC';
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);

}

function changeOrderStatus()
{
    $pdo = getPDO();
    $newOrderStatus = $_POST['orderStatus'] == 0 ? 1 : 0;
    $orderID = intval($_POST['orderID']);

    $query = "UPDATE orders SET is_done = $newOrderStatus WHERE id =" . $orderID;
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    return $newOrderStatus;
}

function getProductsForAdmin()
{
    $pdo = getPDO();
    $query = "SELECT product.*, GROUP_CONCAT(c.ru_name SEPARATOR ', ') AS category
              FROM products AS product
                     LEFT JOIN category_product cp ON product.id = cp.product_id
                     LEFT JOIN categories c ON c.id = cp.category_id
              WHERE is_active = TRUE
              GROUP BY product.id
              ORDER BY id DESC";

    if (!isset($_POST['page'])) {
        $_POST = [];
        $_POST['page'] = 1;
    }

    $offset = ($_POST['page'] - 1) * PRODUCTS_ON_PAGE;
    $query .= " LIMIT $offset, " . PRODUCTS_ON_PAGE;

    $statement = $pdo->prepare($query);
    $statement->execute();
    $result =  $statement->fetchAll(PDO::FETCH_ASSOC);
    $pdo = null;
    if (isset($_POST['requestFrom']) && $_POST['requestFrom'] == 'admin') {
        $result['output'] = drawProductsForAdmin($result);
        $result['pagination'] = getPaginatorForAjax();
    }
    return $result;
}

function drawProductsForAdmin($fetchResult)
{

    $output = '<ul class="page-products__list">';
    foreach ($fetchResult as $product) {
        $output .= '<li class="product-item page-products__item">
                    <b class="product-item__name">' . $product['name'] . '</b>
                    <span class="product-item__field" id="product_id">' . $product['id'] . '</span>
                    <span class="product-item__field">' . $product['price'] . ' руб.</span>
                    <span class="product-item__field">' . $product['category'] . '</span>
                    <span class="product-item__field">' . ($product['is_new'] ? 'да' : 'нет') .'</span>
                    <a href="/route/admin/add?product=' . $product['id'] .'" class="product-item__edit" aria-label="Редактировать"></a>
                    <button class="product-item__delete" value="' . $product['id'] . '"></button>
                    </li>';
    }
    $output .= '</ul>';
    return $output;
}

function disableProduct($id)
{
    $pdo = getPDO();
    $statement = $pdo->prepare("UPDATE products SET is_active = FALSE WHERE id = ?");
    if ($statement->execute(array($id))) {
        return true;
    }
    return false;

}
function validateProduct()
{
    $error = null;
    if ($_POST['productName'] == '') {
        $error .= 'Необходимо заполнить название товара. ';
    }
    if ($_POST['productPrice'] == '') {
        $error .= 'Необходимо указать цену товара. ';
    }
    if (empty($_FILES)) {
        $error .= 'Нужно выбрать фото.  ';
    }
    if ($_POST['categories'] == 'null') {
        $error .= 'Необходимо выбрать категорию. ';
    }
    return $error;
}

function addProduct()
{
    $pdo = getPDO();
    $productID = getMaxProductID() + 1;
    $extension = pathinfo($_FILES[0]['name'], PATHINFO_EXTENSION);
    $path =  $_SERVER['DOCUMENT_ROOT'].'/img/products/product-'. $productID . '.' . $extension;
    $src = '/img/products/product-'. $productID . '.' . $extension;
    $categories = explode(',', $_POST['categories']);
    $productName = $_POST['productName'];
    $isNew = $_POST['isNew'] == 'true' ? 1 : 0;
    $isSale = $_POST['isSale'] == 'true' ? 1 : 0;

    $query = '';

    if(isFileTypeAccept($_FILES[0]['tmp_name'])) {
        if (move_uploaded_file($_FILES[0]['tmp_name'], $path)) {

            $query = 'INSERT INTO products (name, price, src, is_new, is_sale)
                        VALUES (:name, :price, :src, :is_new, :is_sale);';

            $statement = $pdo->prepare($query);
            $result = $statement->execute(array(
                                ':name'    => $productName,
                                ':price'   => floatval($_POST['productPrice']),
                                ':src'     => $src,
                                ':is_new'  => $isNew,
                                ':is_sale' => $isSale
                                ));
            createRelation($categories, $pdo, $pdo->lastInsertId());
            return $result;
        }
    }
}

function createRelation($categories, PDO $pdo, $id)
{
    $err = [];

    foreach ($categories as $category) {
        $stmt = $pdo->prepare('SELECT id FROM categories WHERE name = '. $pdo->quote($category));
        $stmt->execute();
        $categoryID = $stmt->fetchColumn();

        $stmt = $pdo->prepare('INSERT INTO category_product (category_id, product_id)
                                        VALUES (:categoryID, :product_id)');
        $stmt->execute([':categoryID' => $categoryID, ':product_id' => $id]);

        $err[] = $stmt->errorInfo();

    }
    return $err;
}

function isFileTypeAccept($file){
    $fInfo = finfo_open(FILEINFO_MIME_TYPE);
    $chek = in_array(finfo_file($fInfo, $file), ACCEPT_UPLOAD_FILE_TYPES);
    finfo_close($fInfo);
    return $chek;
}

function getMaxProductID()
{
    $pdo = getPDO();
    $statement = $pdo->prepare("SELECT MAX(id) AS maxID FROM products");
    $statement->execute();
    $maxID = $statement->fetch(PDO::FETCH_NUM)[0];
    $pdo = null;
    return $maxID;
}

function changeProduct()
{
    $pdo = getPDO();
    $product = getProductByID($_POST['productID']);
    if (!empty($_FILES)) {
        if(isFileTypeAccept($_FILES[0]['tmp_name'])) {
            copy($_FILES[0]['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $product['src']) ;
        }
    }

    $categories = explode(',', $_POST['categories']);
    $isSale = $_POST['isSale'] == 'true' ? 1 : 0;
    $isNew = $_POST['isNew'] == 'true' ? 1 : 0;

    $query = "UPDATE products SET 
                    name     = :name, 
                    price    = :price,
                    is_sale  = :isSale,
                    is_new   = :isNew
                    WHERE id = :productID;";
    $statement = $pdo->prepare($query);
    $statement->execute([
                        ':name'     => $_POST['productName'],
                        ':price'    => floatval($_POST['productPrice']),
                        ':isSale'   => $isSale,
                        ':isNew'    => $isNew,
                        ':productID'=> intval($product['id']),
                        ]);

    $statement = $pdo->prepare('DELETE FROM category_product
                                        WHERE product_id = ? ');
    $statement->execute([$product['id']]);
    return createRelation($categories, $pdo, $product['id']);
}
