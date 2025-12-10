# 🍔 Food Queue & 🛡️ AllergyPass Integration

A complete restaurant ecosystem that combines a robust **Food Ordering System** with a cutting-edge **Allergen Safety Platform**. This system allows customers to carry a "Digital Medical ID" (QR Code) that automatically filters restaurant menus for their safety.

---

## 🏗️ System Architecture

This project consists of two distinct but integrated applications:

### 1. 🛡️ AllergyPass (The Identity Provider)
* **Location:** `/allergypass/`
* **Purpose:** Allows users to create a verified medical profile and generates a secure QR code.
* **Key Interfaces:**
    * **User App (`index.html`):** Customers select allergies (Dairy, Peanuts, Custom) and generate a High-Definition QR Pass.
    * **Admin Panel (`admin.html`):** Managers define the "Master Dictionary" of allergens (e.g., "Dairy = Milk, Cheese, Whey").
    * **StaffGuard (`test.html`):** A standalone scanner for waiters to check a guest's safety at the table.

### 2. 🍔 Food Queue System (The Service Provider)
* **Location:** `/food_ordering_system/`
* **Purpose:** Handles the restaurant's ordering, kitchen workflow, and digital signage.
* **Key Interfaces:**
    * **Kiosk (`kiosk.html`):** Self-ordering tablet. **Now integrated with a camera** to scan AllergyPass QRs and warn users about unsafe food.
    * **Kitchen Dashboard (`dashboard.html`):** Command center for managing orders, stock, and menu ingredients.
    * **Public Monitor (`monitor.html`):** TV display for "Preparing/Ready" order numbers.

---

## 🚀 Key Features

### 🔄 The Safety Integration
1.  **Scan:** Customer scans their AllergyPass QR at the Kiosk.
2.  **Verify:** The Kiosk silently contacts the AllergyPass database via the **Bridge API**.
3.  **Filter:** The menu automatically updates:
    * **Safe Items:** Appear normally.
    * **Unsafe Items:** Highlighted with a **"⛔ Contains [Allergen]"** badge.
    * **Intervention:** If a user tries to add an unsafe item, a "Medical Alert" confirmation pops up.

### 🛠️ Operational Tools
* **Visual Menu Manager:** Drag-and-drop menu editing with ingredient management.
* **Kanban Kitchen Board:** Track orders from "Pending" to "Ready".
* **Financial History:** Export sales data to CSV.
* **StaffGuard Tool:** A mobile-friendly scanner for waiters to validate dishes manually.

---

## ⚙️ Installation Guide

### 1. Server Requirements
* **XAMPP** (or any LAMP stack) running Apache and MySQL.
* **HTTPS (Optional but Recommended):** Required for phone cameras to work. Use **ngrok** for local testing (`ngrok http 80`).

### 2. Database Setup
You need **two** separate databases.

**Database A: `food_queue` (Restaurant Data)**
1.  Create database: `food_queue`
2.  Import: `food_queue.sql`

**Database B: `allergypass_db` (Safety Data)**
1.  Create database: `allergypass_db`
2.  Import: `allergypass_db.sql`

### 3. File Deployment
Copy the entire project folder to `htdocs`. Ensure the structure looks like this:
