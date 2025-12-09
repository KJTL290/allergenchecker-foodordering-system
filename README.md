# 🍔 AllergenChecker & Food Ordering Queue System

A complete, local-network web application for restaurants to manage orders, kitchen workflows, and menu displays. This system features a customer-facing Kiosk, a Kitchen/Admin Dashboard, and a Public Order Monitor (TV Display).

Built with **PHP, MySQL, and Vanilla JavaScript** (No external frameworks required).

---

## 🚀 Features

### 📱 1. Customer Kiosk (`kiosk.html`)
* **Touch-Friendly Interface:** Designed for tablets/touchscreens.
* **Visual Menu:** Browse food by categories with a sticky sidebar.
* **Smart Cart:** Add items, adjust quantities, and review orders before submitting.
* **Security Lock:** The kiosk is locked by default. Staff must log in to unlock the ordering interface.
* **Auto-Reset:** Automatically resets for the next customer after an order is placed.

### 💻 2. Admin Dashboard (`dashboard.html`)
* **Role-Based Access:**
    * **Admins:** Full access to all settings.
    * **Staff:** Restricted access (Operations & History only).
* **Real-Time Operations:**
    * **Cashier View:** See incoming "Unpaid" orders instantly.
    * **Kitchen Kanban:** Drag-and-drop style status board (Pending → Preparing → Ready).
    * **Quick Stock:** Toggle item availability (Out of Stock) instantly without entering the menu editor.
* **Visual Menu Manager:**
    * **Drag & Drop:** Reorder categories and food items easily.
    * **Product Editor:** Upload images, set prices, and **manage ingredients** (for allergy checking).
* **Financial History:**
    * View total revenue and order counts.
    * Filter by Date Range and Status (Completed/Cancelled).
    * **Export to CSV:** Download reports for accounting.
* **User Management:** Create and delete Staff/Admin accounts.

### 📺 3. Order Monitor (`monitor.html`)
* **Public Display:** Ideal for a large TV screen in the waiting area.
* **Split Screen:** Shows "Preparing" vs "Ready for Pickup" orders.
* **Audio Notifications:** Plays a "Ding" sound whenever a new order is marked as **Ready**.
* **Auto-Update:** Refreshes automatically every 3 seconds.

---

## 🛠️ Tech Stack
* **Frontend:** HTML5, CSS3, JavaScript (Fetch API).
* **Backend:** PHP (Native).
* **Database:** MySQL / MariaDB.
* **Server:** XAMPP (Apache + MySQL).

---

## ⚙️ Installation Guide

1.  **Install XAMPP** or any PHP/MySQL local server environment.
2.  **Setup Database:**
    * Open phpMyAdmin (`http://localhost/phpmyadmin`).
    * Create a new database named **`food_queue`**.
    * Import the included **`food_queue.sql`** file.
3.  **Deploy Files:**
    * Copy the project folder into your `htdocs` directory (e.g., `C:\xampp\htdocs\food_ordering_system`).
    * Ensure the `uploads/` folder exists for product images.
4.  **Configure Connection:**
    * Open `db_connect.php`.
    * Ensure the credentials match your local setup (Default: User `root`, Password ``).

---

## 🔑 Default Login Credentials

Use these accounts to access the Dashboard or Unlock the Kiosk.

| Role | Username | Password | Permissions |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `admin` | `admin123` | Full Access (Menu, Users, History, Ops) |
| **Staff** | `staff` | `staff123` | Limited Access (Operations, History, Stock Toggle) |

---

## 🖥️ How to Run on Local Network

To use this with multiple devices (e.g., Phones as Kiosks, Laptop as Server):

1.  **Find your Host IP:**
    * Open Command Prompt (Windows) and type `ipconfig`.
    * Note your IPv4 Address (e.g., `192.168.1.5`).
2.  **Connect Devices:**
    * **Admin Laptop:** Go to `http://localhost/food_ordering_system/dashboard.html`
    * **Kiosk Tablet:** Go to `http://192.168.1.5/food_ordering_system/kiosk.html`
    * **TV Monitor:** Go to `http://192.168.1.5/food_ordering_system/monitor.html`

---

## 🔮 Future Roadmap
* **Allergy Checker:** Utilize the newly added `ingredients` field to warn customers about allergens (Nuts, Dairy, etc.) before they order.
* **Receipt Printing:** Integrate thermal printer support.
