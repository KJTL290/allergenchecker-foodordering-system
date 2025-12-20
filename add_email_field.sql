-- Add email field to users table
ALTER TABLE users ADD COLUMN email VARCHAR(100) UNIQUE;

-- Update the existing test user with an email
UPDATE users SET email='test@example.com' WHERE username='test';