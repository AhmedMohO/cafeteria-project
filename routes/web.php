<?php

use Core\Router;
use App\Controllers\AuthController;
use App\Controllers\Admin\UserController;
use App\Controllers\Admin\ProductController;
use App\Controllers\Admin\CategoryController;
use App\Controllers\Admin\OrderController;
use App\Controllers\User\ProductController as UserProductController;
use App\Controllers\User\OrderController as UserOrderController;

// Router::get('/user/home', [AuthController::class,'index'], [
//     'AuthMiddleware',
// ]);

Router::get('/login', [AuthController::class, 'loginForm'], ['GuestMiddleware']);
Router::post('/login', [AuthController::class, 'login'], ['GuestMiddleware']);
Router::get('/logout', [AuthController::class, 'logout']);


///

Router::get('/user/home', [UserProductController::class, 'index'], [
    'AuthMiddleware'
]);

Router::get('/user/my-orders', [UserOrderController::class, 'myOrders'], [
    'AuthMiddleware'
]);
Router::post('/user/my-orders/cancel', [UserOrderController::class, 'cancelOrder'], [
    'AuthMiddleware'
]);

Router::get('/user/my_orders.php', [UserOrderController::class, 'myOrders'], [
    'AuthMiddleware'
]);
Router::post('/user/my_orders.php', [UserOrderController::class, 'cancelOrder'], [
    'AuthMiddleware'
]);

Router::get('/user/orders', [UserOrderController::class, 'ordersAlias'], [
    'AuthMiddleware'
]);

Router::get('/user/orders.php', [UserOrderController::class, 'ordersAlias'], [
    'AuthMiddleware'
]);

Router::get('/user/search-products', [UserProductController::class, 'searchProducts'], [
    'AuthMiddleware'
]);
Router::get('/user/latest-order', [UserOrderController::class, 'latestOrder'], [
    'AuthMiddleware'
]);
Router::post('/user/place-order', [UserOrderController::class, 'placeOrder'], [
    'AuthMiddleware'
]);

Router::get('/user/search_products.php', [UserProductController::class, 'searchProducts'], [
    'AuthMiddleware'
]);
Router::get('/user/latest_order.php', [UserOrderController::class, 'latestOrder'], [
    'AuthMiddleware'
]);
Router::post('/user/place_order.php', [UserOrderController::class, 'placeOrder'], [
    'AuthMiddleware'
]);



//*- Users Routes
Router::get('/admin/users', [UserController::class, 'index'], [
    'AuthMiddleware',
    'AdminMiddleware'
]);
Router::post('/admin/users/delete', [UserController::class, 'delete'], [
    'AuthMiddleware',
    'AdminMiddleware'
]);
Router::get('/admin/users/create', [UserController::class, 'create'], [
    'AuthMiddleware',
    'AdminMiddleware'
]);
Router::post('/admin/users', [UserController::class, 'store'], [
    'AuthMiddleware',
    'AdminMiddleware'
]);
Router::get('/admin/users/edit', [UserController::class, 'edit'], [
    'AuthMiddleware',
    'AdminMiddleware'
]);
Router::post('/admin/users/update', [UserController::class, 'update'], [
    'AuthMiddleware',
    'AdminMiddleware'
]);
Router::post('/admin/users/activate', [UserController::class, 'activate'], [
    'AuthMiddleware',
    'AdminMiddleware'
]);
//*- Products Routes
Router::get('/admin/products', [ProductController::class, 'index'], ['AuthMiddleware', 'AdminMiddleware']);
Router::get('/admin/products/create', [ProductController::class, 'create'], ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/products/store', [ProductController::class, 'store'], ['AuthMiddleware', 'AdminMiddleware']);
Router::get('/admin/products/edit/{id}', [ProductController::class, 'edit'], ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/products/update/{id}', [ProductController::class, 'update'], ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/products/delete/{id}', [ProductController::class, 'delete'], ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/products/toggle/{id}', [ProductController::class, 'toggle'], ['AuthMiddleware', 'AdminMiddleware']);

//*- Categories Routes  
Router::get('/admin/categories', [CategoryController::class, 'index'], ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/categories/store', [CategoryController::class, 'store'], ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/categories/delete/{id}', [CategoryController::class, 'delete'], ['AuthMiddleware', 'AdminMiddleware']);

//*- Dashboard Route
Router::get('/admin/dashboard',      [OrderController::class, 'dashboard'],  ['AuthMiddleware', 'AdminMiddleware']);

//*- Orders Routes
Router::get('/admin/orders',         [OrderController::class, 'index'],       ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/mark-delivered',[OrderController::class, 'deliver'],     ['AuthMiddleware', 'AdminMiddleware']);

//*- Manual Order Routes
Router::get('/admin/manual-order',   [OrderController::class, 'manualForm'],  ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/place-order',   [OrderController::class, 'manualStore'], ['AuthMiddleware', 'AdminMiddleware']);

//*- Checks Route
Router::get('/admin/checks',         [OrderController::class, 'checks'],      ['AuthMiddleware', 'AdminMiddleware']);
