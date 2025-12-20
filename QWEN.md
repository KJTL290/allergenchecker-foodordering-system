# Food Queue & AllergyPass Integration System

## Project Overview

This is a complete restaurant ecosystem that combines a robust **Food Ordering System** with a cutting-edge **Allergen Safety Platform**. The system allows customers to carry a "Digital Medical ID" (QR Code) that automatically filters restaurant menus for their safety.

### Architecture

The project consists of two integrated applications:

#### 1. AllergyPass (The Identity Provider)
- **Location:** `/allergypass/`
- **Purpose:** Allows users to create a verified medical profile and generates a secure, scanner-friendly QR code.
- **Key Interfaces:**
  - **User App (`index.html`):** Customers select allergies (Dairy, Peanuts, Custom) and generate a High-Definition (Pixel-Perfect) QR Pass.
  - **Admin Panel (`admin.html`):** Managers define the "Master Dictionary" of allergens (e.g., "Dairy = Milk, Cheese, Whey").
  - **Allergy Display Terminal (`test.html`):** A standalone scanner for waiters to instantly view a guest's medical profile at the table.

#### 2. Food Queue System (The Service Provider)
- **Location:** `/food_ordering_system/`
- **Purpose:** Handles the restaurant's ordering, kitchen workflow, and digital signage.
- **Key Interfaces:**
  - **Kiosk (`kiosk.html`):** Self-ordering tablet. Integrated with a camera to scan AllergyPass QRs and warn users about unsafe food.
  - **Kitchen Dashboard (`dashboard.html`):** Command center for managing orders, stock, menu ingredients, and staff users.
  - **Public Monitor (`monitor.html`):** TV display for "Preparing/Ready" order numbers.

## Key Features

### The Safety Integration
1. **Scan:** Customer scans their AllergyPass QR at the Kiosk.
2. **Verify:** The Kiosk silently contacts the AllergyPass database via the Bridge API.
3. **Filter:** The menu automatically updates:
   - **Safe Items:** Appear normally.
   - **Unsafe Items:** Highlighted with a "⛔ Contains [Allergen]" badge.
   - **Intervention:** If a user tries to add an unsafe item, a "Medical Alert" confirmation pops up.

### Recent Updates (v2.0)
- **HD QR Engine:** Completely rewritten download logic. Uses HTML5 Canvas to generate pixel-perfect, non-blurry QR codes with proper quiet zones for instant scanning.
- **Modern UI/UX:** Replaced all native browser popups with SweetAlert2 for professional, mobile-friendly notifications.
- **Password Visibility:** Added a "Reveal Password" toggle (🔍) to all login and registration screens for better usability.
- **Advanced User Management:** Admins can now Edit existing users (update roles/usernames) and reset passwords directly from the Dashboard.

### Operational Tools
- **Visual Menu Manager:** Drag-and-drop menu editing with ingredient management.
- **Kanban Kitchen Board:** Track orders from "Pending" to "Ready".
- **Financial History:** Export sales data to CSV.
- **Security:** Staff login required to unlock the Kiosk interface.

## Building and Running

### Server Requirements
- **XAMPP** (or any LAMP stack) running Apache and MySQL.
- **HTTPS (Optional but Recommended):** Required for phone cameras to work. Use **ngrok** for local testing (`ngrok http 80`).

### Database Setup
You need **two** separate databases.

**Database A: `food_queue` (Restaurant Data)**
1. Create database: `food_queue`
2. Import: `food_queue.sql`

**Database B: `allergypass_db` (Safety Data)**
1. Create database: `allergypass_db`
2. Import: `allergypass_db.sql`

### File Deployment
Copy the entire project folder to `htdocs`. Ensure the structure looks like this:

```
/htdocs
/allergypass
- index.html
- admin.html
- api.php
/food_ordering_system
- kiosk.html
- dashboard.html
- get_user_profile.php (The Bridge)
```

## Authentication and Sessions

The system uses PHP sessions for authentication with different session types for different applications:

- **Admin Session:** Used for the dashboard and administrative functions
- **Kiosk Session:** Used for the kiosk interface

Authentication is handled via MD5 hashed passwords stored in the database.

## Database Connections

Both systems connect to separate databases:
- **AllergyPass:** Connects to `allergypass_db`
- **Food Ordering System:** Connects to `food_queue`

The food ordering system also connects to the allergypass_db to retrieve allergen dictionaries via `get_allergy_data.php`.

## API Structure

### AllergyPass API (`allergypass/api.php`)
Handles user registration, login, profile management, and allergen data:
- `register`: Create new user account
- `login`: Authenticate user
- `get_profile`: Retrieve user's allergy profile
- `get_common_allergens`: Get available allergen types
- `save_profile`: Save user's selected allergens
- `add_custom_allergy`: Add custom allergen definitions
- `update_custom_allergy`: Update custom allergen definitions
- `delete_custom_allergy`: Remove custom allergen definitions

### Food Ordering System API (`food_ordering_system/api.php`)
Manages orders and history:
- `get_orders`: Retrieve current orders
- `get_history`: Get order history
- `update_status`: Update order status

### Administrative API (`food_ordering_system/admin_api.php`)
Manages products, categories, and menu:
- `get_all`: Retrieve all products
- `save`: Create/update products (with ingredients)
- `delete`: Remove products
- `toggle_stock`: Update product availability
- `get_categories`: Retrieve categories
- `save_category`: Create/update categories
- `delete_category`: Remove categories
- `reorder_products/categories`: Update sort order

### Authentication API (`food_ordering_system/auth.php`)
Handles login/logout for both admin and kiosk modes:
- `login`: Authenticate user for specific application (admin/kiosk)
- `check_session`: Verify session validity
- `logout`: End session
- User management functions for admins

## Bridge Functionality

The system integrates through the `get_allergy_data.php` endpoint, which connects to the allergypass database to retrieve allergen dictionaries. This allows the food ordering system to match ingredients against user allergies in real-time.

## Development Conventions

- Uses PHP for backend logic with MySQL databases
- Frontend built with vanilla JavaScript and HTML5
- Responsive CSS layouts
- Session-based authentication
- MD5 hashing for passwords (though this is not recommended for production)
- JSON for API communications
- HTML5 Canvas for QR code generation
- SweetAlert2 for user notifications

## Testing Instructions

### Local Network Testing
1. On Laptop (Server):
   - Open `http://localhost/food_ordering_system/dashboard.html` to manage the kitchen.
   - Open `http://localhost/food_ordering_system/kiosk.html` to launch the Kiosk.

2. On Phone (Customer):
   - Connect to the same Wi-Fi.
   - Go to `http://[YOUR_LAPTOP_IP]/allergypass/` to create a profile.
   - Download the Image: Use the "Download HD" button.

3. The Interaction:
   - Show the downloaded image to the Laptop's Kiosk camera.
   - Watch the menu react instantly!

## Login Credentials

### Food System Admin (`/food_ordering_system/login.html`)
| Role | Username | Password | Access |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `admin` | `admin123` | Full Control (Menu, Users, History) |
| **Staff** | `staff` | `staff123` | Limited (Kitchen Ops only) |

### AllergyPass Admin (`/allergypass/admin.html`)
| Role | Username | Password | Purpose |
| :--- | :--- | :--- | :--- |
| **Manager** | `admin` | `admin123` | Manage Allergen Dictionary & User Database |

## Troubleshooting

- **Camera not opening?**
  - Check if you are on `https://` or `localhost`. Browsers block cameras on insecure `http://` IPs. Use **ngrok** to tunnel your localhost if testing on a real phone.
- **Scanner not detecting the QR?**
  - Ensure you are using the new downloaded image. Older screenshots might be blurry.
  - Hold the phone about 6 inches away from the camera.
  - Ensure screen brightness is at ~50% (too bright washes out the camera).