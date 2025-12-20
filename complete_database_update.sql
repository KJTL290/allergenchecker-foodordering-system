-- SQL script to update the allergypass_db database for password recovery functionality

-- Add email field to users table
ALTER TABLE `users` ADD COLUMN `email` VARCHAR(100) UNIQUE AFTER `full_name`;

-- Add password reset token fields for secure password recovery
ALTER TABLE `users` ADD COLUMN `reset_token` VARCHAR(255) NULL AFTER `email`;
ALTER TABLE `users` ADD COLUMN `reset_token_expiry` DATETIME NULL AFTER `reset_token`;

-- Update the existing test user with an email (if needed)
-- UPDATE `users` SET `email` = 'test@example.com' WHERE `username` = 'test';

-- Create a backup of the current users table before making changes (optional)
-- CREATE TABLE `users_backup` AS SELECT * FROM `users`;