-- Sample Categories for Testing
-- Run this to populate test categories if needed

INSERT INTO categories (category_name, description, icon) VALUES
('Restaurants', 'Food and dining establishments', 'fas fa-utensils'),
('Healthcare', 'Medical and wellness services', 'fas fa-heartbeat'),
('Retail', 'Shops and retail businesses', 'fas fa-shopping-bag'),
('Technology', 'Tech and IT services', 'fas fa-laptop'),
('Education', 'Schools and training centers', 'fas fa-graduation-cap'),
('Transportation', 'Transport and logistics', 'fas fa-car'),
('Entertainment', 'Entertainment and leisure', 'fas fa-theater-masks'),
('Professional Services', 'Legal, accounting, consulting', 'fas fa-briefcase'),
('Hospitality', 'Hotels and accommodation', 'fas fa-hotel'),
('Beauty & Wellness', 'Salons and spas', 'fas fa-spa')
ON DUPLICATE KEY UPDATE 
category_name = VALUES(category_name),
description = VALUES(description),
icon = VALUES(icon);

-- Sample Test Business Owner User
-- Password: Test@1234 (hashed with password_hash)
INSERT INTO users (email, password, role, first_name, last_name, phone_number, city, state, created_at) VALUES
('businessowner@test.com', '$2y$10$YIjlrBnPXJ8kGXHvCNWg9.L2.5LvKqOp5q8P5mG7qH7qI2J9qE7Ky', 'business_owner', 'John', 'Doe', '+260123456789', 'Lusaka', 'Lusaka', NOW())
ON DUPLICATE KEY UPDATE role = 'business_owner';

-- Verify categories exist
SELECT COUNT(*) as category_count FROM categories;

-- Verify test user exists
SELECT id, email, first_name, role FROM users WHERE email = 'businessowner@test.com';
