# Tefzon Asset Management System

A comprehensive asset management portal for tracking, managing, and monitoring company assets. Built with PHP and MySQL, featuring a responsive frontend interface and a full-featured admin panel.

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Installation](#installation)
- [Usage](#usage)
- [Admin Access](#admin-access)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [Contributing](#contributing)
- [License](#license)

---

## Overview

The **Tefzon Asset Management System** is designed to help organizations track their physical assets efficiently. It provides a complete solution for managing asset inventory, categories, depreciation, and asset details all in one place. The system includes both a public-facing frontend for asset discovery and a protected admin panel for management operations.

---

## Features

### Frontend (Public)
- **Asset Browser**: Browse and search all assets with filtering by category
- **Asset Details**: View complete asset information including images, barcode, and specifications
- **Category Management**: View assets organized by category with asset counts
- **Responsive Design**: Works seamlessly on desktop and mobile devices
- **Modern UI**: Clean interface with gradient themes and interactive elements

### Admin Panel
- **Authentication**: Secure login system with role-based access control
- **Asset Management**: Add, view, edit, and delete assets
- **Category Management**: Create and manage asset categories
- **Image Upload**: Support for multiple image uploads per asset (primary image auto-detection)
- **Barcode Generation**: Automatic barcode generation for each asset using external API
- **Dashboard Statistics**: Overview of total assets, categories, and total asset value
- **Asset Search & Filter**: Advanced filtering by name, code, and category
- **Delete Operations**: Safe asset deletion with confirmation

### Core Functionality
- PDO-based MySQL database connections with prepared statements
- Session-based user authentication
- File upload handling with directory management
- HTML escaping for XSS prevention
- QR/barcode generation via external service

---

## Technology Stack

- **PHP 7.4+** - Server-side scripting language
- **MySQL / MariaDB** - Database management system
- **PDO** - Database abstraction for secure queries
- **HTML5/CSS3** - Markup and styling
- **Vanilla JavaScript** - Client-side interactivity
- **Barcode.tec-it.com API** - Barcode image generation

---

## Installation

### Prerequisites

- XAMPP/WAMP/MAMP (or any PHP/MySQL development environment)
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Setup Steps

1. **Clone the repository** (or download the project files):
   ```bash
   git clone https://github.com/VeridonHQ-Dev/Tefzon-Assetmgt-Portal.git
   cd Tefzon-Assetmgt-Portal
   ```

2. **Start your local server**:
   - If using XAMPP: Start Apache and MySQL from the XAMPP control panel

3. **Create the database**:
   - Open `http://localhost/phpmyadmin` in your browser
   - Create a new database named `tefzon_assets`
   - Import the database schema: `database.sql`

4. **Configure the application**:
   - Verify `config.php` has correct database settings:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');  // Change if using a password
     define('DB_NAME', 'tefzon_assets');
     define('SITE_URL', 'http://localhost/tefzon');
     define('UPLOAD_PATH', 'uploads/');
     ```

5. **Set up file permissions**:
   - Ensure the `uploads/` directory is writable:
     ```bash
     chmod 755 uploads/
     chmod 755 uploads/assets/
     chmod 755 uploads/barcodes/
     ```

6. **Access the application**:
   - Frontend: `http://localhost/tefzon/`
   - Admin Panel: `http://localhost/tefzon/admin/`

---

## Usage

### Frontend User

1. **Homepage**: View all assets with search and category filtering
2. **Asset Details**: Click on any asset card to view detailed information
3. **Category Browsing**: Use the category selector to filter assets
4. **Search**: Use the search bar to find assets by name or asset code

### Admin User

1. **Login**: Access the admin panel at `http://localhost/tefzon/admin/`
   - Default credentials (see Admin Access section below)
2. **Dashboard**: View statistics overview including total assets, categories, and value
3. **Assets Management**:
   - View all assets in a searchable table
   - Click "View" to see detailed asset information
   - Click "Add New Asset" to add a new asset
   - Use delete buttons to remove assets (with confirmation)
4. **Categories Management**:
   - Add new categories via the "Add New Category" form
   - Delete categories (with safety check to prevent deleting categories with assets)
5. **Add New Asset**:
   - Fill in asset details including name, category, description, specifications
   - Set purchase date, price, condition status, and location
   - Upload multiple images (first image becomes primary)
   - Barcode is automatically generated
   - Submit to add the asset to the system

---

## Admin Access

### Default Administrator Credentials

| Field | Value |
|-------|-------|
| **Username** | `admin` |
| **Password** | `admin123` |
| **Email** | `admin@tefzon.com` |
| **Role** | `admin` |

**Important**: Change the default password after first login for security purposes.

### Admin Panel Routes

- `admin/dashboard.php` - Dashboard overview with statistics
- `admin/assets.php` - Manage existing assets (view, add, delete)
- `admin/add_asset.php` - Form to add new assets with image uploads
- `admin/categories.php` - Manage asset categories
- `admin/index.php` - Admin login and session management
- `../logout.php` - Logout and destroy session

---

## Project Structure

```
Tefzon-Assetmgt-Portal/
├── index.php              # Frontend homepage
├── asset.php              # Individual asset detail page
├── categories.php         # Categories listing page
├── config.php             # Database config and helper functions
├── login.php            # User login page
├── logout.php           # User logout handler
├── database.sql           # Database schema and seed data
├── .gitignore            # Git ignore rules
├── README.md             # This file
│
├── uploads/               # Uploaded files directory
│   ├── assets/            # Asset images
│   └── barcodes/          # Generated barcodes
│
└── admin/                 # Admin panel
    ├── dashboard.php      # Admin dashboard
    ├── assets.php         # Asset management
    ├── add_asset.php     # Add new asset form
    ├── categories.php    # Category management
    ├── barcode.php       # Barcode generation
    ├── index.php        # Admin login
    └── admin_styles.css # Admin panel styles
```

---

## Database Schema

### Tables

| Table | Description |
|-------|-------------|
| `users` | User accounts with roles (admin/user) |
| `categories` | Asset categories with asset counts |
| `assets` | Main asset records with all metadata |
| `asset_images` | Multiple images per asset (one primary) |

### Key Fields

**assets table**:
- `asset_code` - Unique asset code (format: AST + timestamp)
- `name` - Asset name (required)
- `category_id` - Foreign key to categories
- `condition_status` - Enum: Excellent, Good, Fair, Poor
- `current_value` - Current asset value
- `purchase_date` - Date of purchase
- `purchase_price` - Original purchase price
- `location` - Physical location
- `barcode_path` - Path to generated barcode image

**asset_images table**:
- `asset_id` - Foreign key to assets
- `image_path` - Path to uploaded image
- `is_primary` - Boolean flag for primary image

---

## Contributing

### Guidelines

1. **Fork the repository** and create a new branch for your feature
2. **Follow existing code styles** - match the existing PHP/HTML/CSS patterns
3. **Maintain security practices**:
   - Always use prepared statements (PDO)
   - Escape all output with `htmlspecialchars()`
   - Validate and sanitize user inputs
4. **Test your changes**:
   - Ensure login/logout still works
   - Verify asset CRUD operations
   - Test image uploads
5. **Commit with clear messages** describing the change
6. **Update README.md** if adding new features or changing usage

### Pull Request Process

1. Ensure all tests pass
2. Update documentation if needed
3. Verify no breaking changes
4. Submit PR with clear description of changes

---

## License

This project is open source and available under the [MIT License](LICENSE). Feel free to use, modify, and distribute this project with attribution.

---

## Support

For issues, feature requests, or questions:
- Open an issue on the GitHub repository
- Contact the development team
- Refer to the wiki for additional documentation