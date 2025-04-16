-- SQL Views for Webstore

-- View: Customer Order Summary
CREATE OR REPLACE VIEW view_customer_orders AS
SELECT 
    o.order_id,
    c.first_name,
    c.last_name,
    o.order_date,
    o.status,
    o.total_amount
FROM `Order` o
JOIN Customer c ON o.customer_id = c.customer_id;

-- View: Order with Items
CREATE OR REPLACE VIEW view_order_items AS
SELECT 
    o.order_id,
    c.first_name,
    c.last_name,
    p.name AS product_name,
    oi.quantity,
    oi.price_at_order_time,
    (oi.quantity * oi.price_at_order_time) AS item_total
FROM OrderItem oi
JOIN `Order` o ON oi.order_id = o.order_id
JOIN Product p ON oi.product_id = p.product_id
JOIN Customer c ON o.customer_id = c.customer_id;

-- View: Inventory Overview
CREATE OR REPLACE VIEW view_inventory AS
SELECT 
    product_id,
    name,
    quantity_in_stock,
    CASE 
        WHEN quantity_in_stock = 0 THEN 'Out of Stock'
        WHEN quantity_in_stock < 10 THEN 'Low Stock'
        ELSE 'In Stock'
    END AS stock_status
FROM Product;
