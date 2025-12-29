Migration steps to apply schema updates made in repository:

1) Backup your database before running any migration.

2) Update `orders.status` to include `cancelled`:

ALTER TABLE `orders`
  MODIFY COLUMN `status` ENUM('unpaid','pending','preparing','ready','completed','cancelled') NOT NULL DEFAULT 'unpaid';

3) Update `users.role` to include `kitchen` and `cashier`:

ALTER TABLE `users`
  MODIFY COLUMN `role` ENUM('admin','staff','kitchen','cashier') NOT NULL DEFAULT 'admin';

4) Fix existing rows with empty status (optional if you have orders that vanished after cancel operations):

UPDATE `orders` SET `status`='cancelled' WHERE `status` = '' OR `status` IS NULL;

5) Verify changes:

SELECT `id`, `status` FROM `orders` ORDER BY `id` DESC LIMIT 10;
SELECT `id`, `username`, `role` FROM `users` ORDER BY `id` DESC LIMIT 10;

6) Restart your web server (if necessary). If you use caching for DB schemas in your environment, ensure it is refreshed.

Notes:
- These SQL modifications mirror the changes in `food_queue.sql` included in the repo.
- Always run migrations on a staging or local copy first to verify.
