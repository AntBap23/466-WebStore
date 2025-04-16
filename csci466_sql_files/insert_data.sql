-- SQL Sample Data for Webstore (Requirement: Step 5)

-- Customers
INSERT INTO Customer (first_name, last_name, email, phone_number) VALUES
('Alice', 'Smith', 'alice@example.com', '555-1234'),
('Bob', 'Johnson', 'bob@example.com', '555-2345'),
('Carol', 'Davis', 'carol@example.com', '555-3456'),
('Dave', 'Lee', 'dave@example.com', '555-4567'),
('Eve', 'Miller', 'eve@example.com', '555-5678');

-- Products (20 minimum)
INSERT INTO Product (name, description, price, quantity_in_stock) VALUES
('Gaming Mouse', 'Ergonomic mouse with RGB.', 49.99, 100),
('Mechanical Keyboard', 'Tactile switches, backlit.', 89.99, 50),
('Laptop Stand', 'Adjustable and aluminum build.', 29.99, 75),
('Wireless Charger', '10W fast charging.', 19.99, 200),
('Noise Cancelling Headphones', 'ANC and Bluetooth.', 129.99, 40),
('Webcam', '1080p resolution.', 59.99, 60),
('USB-C Hub', '6-in-1 with HDMI.', 34.99, 90),
('External HDD 2TB', 'USB 3.0 external storage.', 79.99, 70),
('Smart LED Bulb', '16M colors, Wi-Fi enabled.', 14.99, 150),
('Bluetooth Speaker', 'Compact and loud.', 39.99, 110),
('Portable Monitor', '15.6" USB-C screen.', 179.99, 30),
('Fitness Tracker', 'Heart rate and steps.', 49.99, 100),
('Wireless Mouse', 'Silent click version.', 24.99, 80),
('Phone Tripod', 'Adjustable stand.', 18.99, 95),
('Drawing Tablet', 'Digital pen included.', 99.99, 45),
('LED Desk Lamp', 'Adjustable brightness.', 22.99, 120),
('Smart Plug', 'App controlled plug.', 12.99, 160),
('Gaming Chair', 'Ergonomic support.', 159.99, 20),
('HDMI Cable', '4K compatible.', 8.99, 140),
('VR Headset', 'Immersive experience.', 299.99, 10);

-- Carts
INSERT INTO Cart (customer_id) VALUES
(1), (2), (3), (4), (5);

-- Cart Items
INSERT INTO CartItem (cart_id, product_id, quantity) VALUES
(1, 1, 2), (1, 2, 1), (2, 3, 1), (3, 4, 2), (4, 5, 1), (5, 6, 3);

-- Orders (at least one per customer)
INSERT INTO `Order` (customer_id, status, total_amount) VALUES
(1, 'Processing', 139.97),
(2, 'Shipped', 29.99),
(3, 'Delivered', 39.98),
(4, 'Processing', 129.99),
(5, 'Processing', 179.97);

-- Order Items
INSERT INTO OrderItem (order_id, product_id, quantity, price_at_order_time) VALUES
(1, 1, 2, 49.99),
(1, 2, 1, 39.99),
(2, 3, 1, 29.99),
(3, 4, 2, 19.99),
(4, 5, 1, 129.99),
(5, 6, 3, 59.99);

-- Payments
INSERT INTO Payment (order_id, payment_method, amount) VALUES
(1, 'credit_card', 139.97),
(2, 'paypal', 29.99),
(3, 'credit_card', 39.98),
(4, 'paypal', 129.99),
(5, 'credit_card', 179.97);

-- Addresses (shipping + billing)
INSERT INTO Address (customer_id, type, street, city, state, zip) VALUES
(1, 'shipping', '123 Main St', 'DeKalb', 'IL', '60115'),
(1, 'billing', '123 Main St', 'DeKalb', 'IL', '60115'),
(2, 'shipping', '456 Maple Ave', 'Naperville', 'IL', '60540'),
(3, 'billing', '789 Oak Ln', 'Chicago', 'IL', '60616'),
(4, 'shipping', '321 Birch Dr', 'Sycamore', 'IL', '60178'),
(5, 'billing', '654 Pine Rd', 'Rockford', 'IL', '61101');
