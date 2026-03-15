use cafeteria;

-- Rooms
INSERT INTO rooms (no, name, created_at, updated_at) VALUES
('101', 'Conference Room A', NOW(), NOW()),
('102', 'Conference Room B', NOW(), NOW()),
('201', 'Executive Suite', NOW(), NOW()),
('202', 'Marketing Department', NOW(), NOW()),
('301', 'IT Department', NOW(), NOW());

-- Users (passwords are bcrypt hashes of 'password123')
INSERT INTO users (name, email, password, role, pic, room_id, ext, created_at, updated_at) VALUES
('Admin User',    'admin@cafeteria.com',   '$2y$10$zWRV5LZ6jWzU6/unJVMHhuULfmlKmamF2e6Z/osz1Y43M7eYZ9BO.', 'admin', 'pics/admin.jpg',   1, '1000', NOW(), NOW()),
('Alice Johnson', 'alice@cafeteria.com',   '$2y$10$zWRV5LZ6jWzU6/unJVMHhuULfmlKmamF2e6Z/osz1Y43M7eYZ9BO.', 'user',  'pics/alice.jpg',   2, '1001', NOW(), NOW()),
('Bob Smith',     'bob@cafeteria.com',     '$2y$10$zWRV5LZ6jWzU6/unJVMHhuULfmlKmamF2e6Z/osz1Y43M7eYZ9BO.', 'user',  'pics/bob.jpg',     3, '1002', NOW(), NOW()),
('Carol White',   'carol@cafeteria.com',   '$2y$10$zWRV5LZ6jWzU6/unJVMHhuULfmlKmamF2e6Z/osz1Y43M7eYZ9BO.', 'user',  'pics/carol.jpg',   4, '1003', NOW(), NOW()),
('David Brown',   'david@cafeteria.com',   '$2y$10$zWRV5LZ6jWzU6/unJVMHhuULfmlKmamF2e6Z/osz1Y43M7eYZ9BO.', 'user',  'pics/david.jpg',   5, '1004', NOW(), NOW());

-- Categories
INSERT INTO categories (name, created_at, updated_at) VALUES
('Main Course',  NOW(), NOW()),
('Snacks',       NOW(), NOW()),
('Beverages',    NOW(), NOW()),
('Desserts',     NOW(), NOW()),
('Breakfast',    NOW(), NOW());

-- Products
INSERT INTO products (category_id, name, price, image, status, created_at, updated_at) VALUES
(1, 'Grilled Chicken Rice',    25000.00, 'products/chicken_rice.jpg',    'available',   NOW(), NOW()),
(1, 'Beef Rendang',            30000.00, 'products/beef_rendang.jpg',    'available',   NOW(), NOW()),
(1, 'Vegetable Stir Fry',      20000.00, 'products/veg_stirfry.jpg',     'available',   NOW(), NOW()),
(1, 'Fried Fish',              22000.00, 'products/fried_fish.jpg',      'unavailable', NOW(), NOW()),
(2, 'Spring Rolls (3 pcs)',    10000.00, 'products/spring_rolls.jpg',    'available',   NOW(), NOW()),
(2, 'French Fries',            12000.00, 'products/french_fries.jpg',    'available',   NOW(), NOW()),
(2, 'Fried Tofu',               8000.00, 'products/fried_tofu.jpg',      'available',   NOW(), NOW()),
(3, 'Mineral Water',            5000.00, 'products/mineral_water.jpg',   'available',   NOW(), NOW()),
(3, 'Orange Juice',            12000.00, 'products/orange_juice.jpg',    'available',   NOW(), NOW()),
(3, 'Iced Tea',                 8000.00, 'products/iced_tea.jpg',        'available',   NOW(), NOW()),
(3, 'Hot Coffee',              10000.00, 'products/hot_coffee.jpg',      'available',   NOW(), NOW()),
(4, 'Chocolate Pudding',       15000.00, 'products/choc_pudding.jpg',    'available',   NOW(), NOW()),
(4, 'Fruit Salad',             13000.00, 'products/fruit_salad.jpg',     'unavailable', NOW(), NOW()),
(5, 'Nasi Uduk',               18000.00, 'products/nasi_uduk.jpg',       'available',   NOW(), NOW()),
(5, 'Oatmeal with Fruits',     16000.00, 'products/oatmeal.jpg',         'available',   NOW(), NOW());

-- Orders
INSERT INTO orders (user_id, room_id, total_price, status, notes, created_at, updated_at) VALUES
(2, 2, 57000.00, 'done',             'Please deliver before 12:00',  NOW(), NOW()),
(3, 3, 40000.00, 'out_for_delivery', 'No spicy food please',         NOW(), NOW()),
(4, 4, 33000.00, 'processing',       NULL,                           NOW(), NOW()),
(5, 5, 46000.00, 'done',             'Extra napkins please',         NOW(), NOW()),
(2, 2, 28000.00, 'processing',       'Leave at the door',            NOW(), NOW());

-- Order Items
INSERT INTO order_items (order_id, product_id, name, price, image, quantity, created_at, updated_at) VALUES
-- Order 1 (Alice): Grilled Chicken Rice x2 + Iced Tea x1
(1, 1,  'Grilled Chicken Rice', 25000.00, 'products/chicken_rice.jpg',  2, NOW(), NOW()),
(1, 10, 'Iced Tea',              8000.00, 'products/iced_tea.jpg',       1, NOW(), NOW()),

-- Order 2 (Bob): Beef Rendang x1 + Orange Juice x1
(2, 2,  'Beef Rendang',         30000.00, 'products/beef_rendang.jpg',  1, NOW(), NOW()),
(2, 9,  'Orange Juice',         10000.00, 'products/orange_juice.jpg',  1, NOW(), NOW()),

-- Order 3 (Carol): Vegetable Stir Fry x1 + Spring Rolls x1
(3, 3,  'Vegetable Stir Fry',   20000.00, 'products/veg_stirfry.jpg',   1, NOW(), NOW()),
(3, 5,  'Spring Rolls (3 pcs)', 10000.00, 'products/spring_rolls.jpg',  1, NOW(), NOW()),

-- Order 4 (David): Nasi Uduk x2 + Hot Coffee x1
(4, 14, 'Nasi Uduk',            18000.00, 'products/nasi_uduk.jpg',     2, NOW(), NOW()),
(4, 11, 'Hot Coffee',           10000.00, 'products/hot_coffee.jpg',    1, NOW(), NOW()),

-- Order 5 (Alice): French Fries x1 + Mineral Water x2
(5, 6,  'French Fries',         12000.00, 'products/french_fries.jpg',  1, NOW(), NOW()),
(5, 8,  'Mineral Water',         5000.00, 'products/mineral_water.jpg', 2, NOW(), NOW());