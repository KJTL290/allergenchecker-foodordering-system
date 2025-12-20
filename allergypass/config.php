<?php
/*
 * Email Configuration for Password Recovery
 *
 * To enable password recovery emails via Gmail:
 * 1. Enable 2-factor authentication on your Gmail account
 * 2. Generate an App Password: https://myaccount.google.com/apppasswords
 * 3. Replace the placeholder values below with your actual Gmail credentials
 * 4. Make sure to use an App Password, not your regular Gmail password
 *
 * IMPORTANT: Update these values with valid email credentials for the password recovery to work
 */

define('EMAIL_HOST', 'smtp.gmail.com');
define('EMAIL_SMTP_AUTH', true);
define('EMAIL_USERNAME', 'kimjoshualopez30@gmail.com'); // Replace with your Gmail address
define('EMAIL_PASSWORD', 'pfhyecgealqzjhlj');             // Replace with your Gmail app password (without spaces)
define('EMAIL_PORT', 587);
define('EMAIL_FROM_ADDRESS', 'kimjoshualopez30@gmail.com');
define('EMAIL_FROM_NAME', 'AllergyPass Support');
?>