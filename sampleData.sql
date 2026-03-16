use cafeteria;

-- Rooms
INSERT INTO rooms (no, name, created_at, updated_at) VALUES
('101', 'Conference Room A', NOW(), NOW()),
('102', 'Conference Room B', NOW(), NOW()),
('201', 'Executive Suite', NOW(), NOW()),
('202', 'Marketing Department', NOW(), NOW()),
('301', 'IT Department', NOW(), NOW());

-- Users (passwords are bcrypt hashes of '123456')
INSERT INTO users (name, email, password, role, pic, room_id, ext, created_at, updated_at) VALUES
('admin','admin@cafeteria.com','$2y$12$qg3A5MtNm3ZBUmzX7lkZzOtsxNpP0O8.D.Erq830fiOYcU6e0hhF.','admin','/upload/profiles/admin.jpeg',5,'245',NOW(),NOW()),
('Alice Johnson', 'alice@cafeteria.com',   '$2y$10$zWRV5LZ6jWzU6/unJVMHhuULfmlKmamF2e6Z/osz1Y43M7eYZ9BO.', 'user',  '/upload/profiles/Alice.jpeg',   2, '1001', NOW(), NOW()),
('Bob Smith',     'bob@cafeteria.com',     '$2y$10$zWRV5LZ6jWzU6/unJVMHhuULfmlKmamF2e6Z/osz1Y43M7eYZ9BO.', 'user',  '/upload/profiles/Bob.jpeg',     3, '1002', NOW(), NOW()),
('Carol White',   'carol@cafeteria.com',   '$2y$10$zWRV5LZ6jWzU6/unJVMHhuULfmlKmamF2e6Z/osz1Y43M7eYZ9BO.', 'user',  '/upload/profiles/Carol.jpeg',   4, '1003', NOW(), NOW()),
('David Brown',   'david@cafeteria.com',   '$2y$10$zWRV5LZ6jWzU6/unJVMHhuULfmlKmamF2e6Z/osz1Y43M7eYZ9BO.', 'user',  '/upload/profiles/David.jpeg',   5, '1004', NOW(), NOW());

-- Categories
INSERT INTO categories (name, created_at, updated_at) VALUES
('Main Course',  NOW(), NOW()),
('Snacks',       NOW(), NOW()),
('Beverages',    NOW(), NOW()),
('Desserts',     NOW(), NOW()),
('Breakfast',    NOW(), NOW());

-- Products
INSERT INTO products (category_id, name, price, image, status, created_at, updated_at) VALUES
(1, 'Grilled Chicken Rice',    200.00, 'Grilled Chicken Rice.jpeg',    'available',   NOW(), NOW()),
(1, 'Beef Rendang',            300.00, 'Beef Rendang.jpeg',            'available',   NOW(), NOW()),
(1, 'Vegetable Stir Fry',      200.00, 'Vegetable Stir Fry.jpeg',      'available',   NOW(), NOW()),
(1, 'Fried Fish',              220.00, 'Fried Fish.jpeg',              'unavailable', NOW(), NOW()),
(2, 'Spring Rolls (3 pcs)',    100.00, 'Spring Rolls.jpeg',            'available',   NOW(), NOW()),
(2, 'French Fries',            120.00, 'French Fries.jpeg',            'available',   NOW(), NOW()),
(2, 'Fried Tofu',               80.00, 'Fried Tofu.jpeg',              'available',   NOW(), NOW()),
(3, 'Mineral Water',            50.00, 'Mineral Water.jpeg',           'available',   NOW(), NOW()),
(3, 'Orange Juice',            120.00, 'Orange Juice.jpeg',            'available',   NOW(), NOW()),
(3, 'Iced Tea',                 80.00, 'Iced Tea.jpeg',                'available',   NOW(), NOW()),
(3, 'Hot Coffee',              100.00, 'Hot Coffee.jpeg',              'available',   NOW(), NOW()),
(4, 'Chocolate Pudding',       150.00, 'Chocolate Pudding.jpeg',       'available',   NOW(), NOW()),
(4, 'Fruit Salad',             130.00, 'Fruit Salad.jpeg',             'unavailable', NOW(), NOW()),
(5, 'Nasi Uduk',               180.00, 'Nasi Uduk.jpeg',               'available',   NOW(), NOW()),
(5, 'Oatmeal with Fruits',     160.00, 'Oatmeal with Fruits.jpeg',     'available',   NOW(), NOW());

-- Orders
INSERT INTO orders (user_id, room_id, total_price, status, notes, created_at, updated_at) VALUES
(2, 2, 480.00, 'done',             'Please deliver before 12:00',  NOW(), NOW()),
(3, 3, 420.00, 'out_for_delivery', 'No spicy food please',         NOW(), NOW()),
(4, 4, 300.00, 'processing',       NULL,                           NOW(), NOW()),
(5, 5, 460.00, 'done',             'Extra napkins please',         NOW(), NOW()),
(2, 2, 220.00, 'processing',       'Leave at the door',            NOW(), NOW());


-- Order Items (FIXED PRICES)
INSERT INTO order_items (order_id, product_id, name, price, image, quantity, created_at, updated_at) VALUES
-- Order 1
(1, 1,  'Grilled Chicken Rice', 200.00, 'Grilled Chicken Rice.jpeg', 2, NOW(), NOW()),
(1, 10, 'Iced Tea',              80.00, 'Iced Tea.jpeg',             1, NOW(), NOW()),

-- Order 2
(2, 2,  'Beef Rendang',         300.00, 'Beef Rendang.jpeg',         1, NOW(), NOW()),
(2, 9,  'Orange Juice',         120.00, 'Orange Juice.jpeg',         1, NOW(), NOW()),

-- Order 3
(3, 3,  'Vegetable Stir Fry',   200.00, 'Vegetable Stir Fry.jpeg',   1, NOW(), NOW()),
(3, 5,  'Spring Rolls (3 pcs)', 100.00, 'Spring Rolls.jpeg',         1, NOW(), NOW()),

-- Order 4
(4, 14, 'Nasi Uduk',            180.00, 'Nasi Uduk.jpeg',            2, NOW(), NOW()),
(4, 11, 'Hot Coffee',           100.00, 'Hot Coffee.jpeg',           1, NOW(), NOW()),

-- Order 5
(5, 6,  'French Fries',         120.00, 'French Fries.jpeg',         1, NOW(), NOW()),
(5, 8,  'Mineral Water',         50.00, 'Mineral Water.jpeg',        2, NOW(), NOW());