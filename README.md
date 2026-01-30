# Restaurant Ordering System

A simple **restaurant ordering web application** built using **PHP, MySQL, HTML, CSS, and Bootstrap**. This project allows users to browse menu items, add them to a cart, place orders, and manage their profiles.

---

## Project Deliverables

* Project source code (ZIP file or Git repository)
* SQL file (database schema)
* README.md containing:

  * Setup instructions
  * Database configuration
  * How to run the project locally
  * Notes or assumptions

## Tech Stack

* **Frontend:** HTML5, CSS3, Bootstrap 5
* **Backend:** PHP
* **Database:** MySQL
* **Server:** XAMPP / WAMP / LAMP

---

## Setup Instructions

1. Clone or download the project repository:

   ```bash
   git clone https://github.com/cpevillanuevagabrielandrei-art/restaurant-ordering.git
   ```

2. Move the project folder into your local server directory:

   * XAMPP: `htdocs/`
   * WAMP: `www/`

3. Start **Apache** and **MySQL** from your control panel.

---

## Database Configuration

1. Open **phpMyAdmin**

2. Create a database named:

   ```
   restaurant_ordering
   ```

3. Import the SQL file provided in the project root:

   ```
   restaurant.sql
   ```

4. Configure your database connection in `config/db.php`:

   ```php
   $host = "localhost";
   $user = "root";
   $password = "";
   $database = "restaurant_ordering";
   ```

---

## How to Run the Project Locally

1. Open your browser
2. Go to:

   ```
   http://localhost/restaurant-ordering
   ```
3. Register a new account
4. Browse menu items
5. Add items to cart and place an order
6. View order history and update profile

---

## Database Schema Overview

The application uses the following tables:

* **users** – stores registered user information
* **products** – stores menu items
* **orders** – stores user orders
* **order_items** – stores items per order

### Order Status Values

* `Pending`
* `Completed`

---

## Notes & Assumptions

* Passwords are stored using hashing for security
* Cart functionality is session-based
* One user can place multiple orders
* Images are stored as file paths

---

## Author

**Gabriel Andrei C. Villanueva**
4th Year Computer Engineering Student
Pamantasan ng Lungsod ng San Pablo

---

## 📄 License

This project is for **educational purposes only**.
