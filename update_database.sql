-- SQL script to add email field to users table and update the database structure

-- Add email field to users table
ALTER TABLE `users` ADD COLUMN `email` VARCHAR(100) UNIQUE AFTER `full_name`;

-- Add email field to the existing test user (if needed)
-- UPDATE `users` SET `email` = 'test@example.com' WHERE `username` = 'test';

-- Add a password reset token field for more secure password recovery
ALTER TABLE `users` ADD COLUMN `reset_token` VARCHAR(255) NULL AFTER `email`;
ALTER TABLE `users` ADD COLUMN `reset_token_expiry` DATETIME NULL AFTER `reset_token`;

-- Create a backup of the current users table before making changes
CREATE TABLE `users_backup` AS SELECT * FROM `users`;