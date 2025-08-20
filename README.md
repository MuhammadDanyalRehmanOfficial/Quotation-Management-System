
# Quotation Management System

A web-based application for managing customers, items, terms, and quotations. This system streamlines the quotation creation process from customer and item selection to finalizing and printing professional quotations.

## ✨ Features

### 🔐 User Authentication
- Secure login system.
- User session management.

### 👥 Customer Management
- Add new customers.
- Edit and manage existing customer records.
- View customer details.

### 📦 Item Management
- Add new items with:
  - Name
  - Category
  - Cost Price
  - Sale Price
  - GST Percentage
  - Barcode
- Edit and manage item records.

### 📄 Terms & Conditions Management
- Create a library of reusable terms and conditions.
- Add, edit, and delete terms.

### 📑 Quotation Creation
- Multi-step process:
  - Select customer.
  - Set date, time, and validity.
  - Add items by name or barcode.
  - Adjust quantities and prices dynamically.
  - Automatically calculate totals (including GST).
  - Select and apply relevant terms and conditions.

### 🖨️ Quotation Output
- Save finalized quotations.
- Generate and print quotation documents.

---

## 🛠️ Technology Stack

| Layer       | Technology         |
|-------------|--------------------|
| Frontend    | HTML, CSS, Bootstrap |
| Backend     | PHP                |
| Database    | MySQL              |

---

## 🚀 Getting Started

Follow these steps to set up the project locally.

### 1. Clone the Repository

```bash
git clone [repository-url]
cd quotation-management-system
````

### 2. Configure the Database

* Open `config/config.php` and set your MySQL connection details.
* Import the required SQL schema (tables for customers, items, terms, quotations).

### 3. Launch the Application

Use a local server environment like **XAMPP**, **WAMP**, or **MAMP**:

* Place the project folder in your server's `htdocs` or equivalent directory.
* Start your Apache and MySQL services.
* Open your browser and go to:

```
http://localhost/quotation-management-system
```

---

## 📁 Project Structure

```
quotation-management-system/
├── config/
│   └── config.php             # Database connection
├── customers/
│   ├── add_customer.php
│   ├── edit_customer.php
│   └── manage_customers.php
├── includes/
│   ├── footer.php
│   ├── header.php
│   └── navbar.php
├── items/
│   ├── add_item.php
│   ├── edit_item.php
│   └── manage_items.php
├── quotations/
│   ├── add_items.php
│   ├── ajax_actions.php
│   ├── ajax_beacon.php
│   ├── create_quotation.php
│   ├── finalize.php
│   ├── print.php
│   └── select_terms.php
├── terms/
│   ├── add_terms.php
│   ├── edit_terms.php
│   └── manage_terms.php
├── dashboard.php
├── index.php                 # Login page
├── logout.php
└── register.php
```

---

## 📌 Notes

* Ensure your MySQL database is running and the credentials in `config/db.php` are correct.
* If needed, create database tables manually or import from a provided `.sql` file (included here in `config`).
* For any AJAX functionality to work, JavaScript should be enabled in the browser.

---

## 📃 License

This project is for educational and internal business use. For other usage, please get in touch with the author.
