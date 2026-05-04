# products_webapi 🚀
A RESTful CRUD Web API built with **PHP** and **MySQL**.

---

## 🛠️ Tech Stack
- PHP
- MySQL
- Laragon (local server)

---

## ⚙️ Setup Instructions

### 1. Requirements
- [Laragon](https://laragon.org/download/) (Apache + MySQL + PHP)
- [Postman](https://www.postman.com/downloads/) for testing

### 2. Clone the repo
```bash
git clone https://github.com/YOUR_USERNAME/WebAPI_PHP.git
```
Place the folder inside:
```
C:\laragon\www\WebAPI_PHP\
```

### 3. Setup the database
- Open Laragon => click **Start All**
- Open your preferred MySQL client and run `script.sql`:
  - **phpMyAdmin** => go to SQL tab => paste and run
  - **DBeaver** => right click database => SQL Editor → paste and run
  - **dbForge** => open SQL editor => paste and run
  - **Terminal** => `mysql -u root -p webapi_db < script.sql`

### 4. Configure database connection
Create `config/connection_db.php` (excluded from repo for security):
```php
<?php
$hostname = "localhost";
$username = "your_mysql_username";
$password = "your_mysql_password";
$dbname   = "webapi_db";
$port     = 3306;

$conn = new mysqli($hostname, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}
```

### 5. Test the API
Open Postman and hit:
```
GET http://localhost/api/products.php
```

---

## 📌 API Endpoints

### 🗂️ Categories
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/categories.php` | Get all categories |
| GET | `/api/categories.php?id=1` | Get one category |
| POST | `/api/categories.php` | Create a category |
| PUT | `/api/categories.php?id=1` | Update a category |
| DELETE | `/api/categories.php?id=1` | Delete a category |

**POST / PUT body:**
```json
{
    "category_name": "Laptops",
    "slug": "laptops"
}
```

---

### 📦 Products
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/products.php` | Get all products |
| GET | `/api/products.php?id=1` | Get one product |
| GET | `/api/products.php?search=laptop` | Search products |
| GET | `/api/products.php?category=laptops` | Filter by category |
| GET | `/api/products.php?search=laptop&category=laptops` | Search + filter |
| GET | `/api/products.php?limit=5&skip=0` | Pagination |
| POST | `/api/products.php` | Create a product |
| PUT | `/api/products.php?id=1` | Update a product |
| DELETE | `/api/products.php?id=1` | Delete a product |

**POST body:**
```json
{
    "product_name": "Gaming Mouse",
    "product_title": "Gaming Mouse Pro RGB",
    "SKU": "GM-PRO-001",
    "brand": "Bodaro",
    "description": "High precision gaming mouse",
    "tags": "gaming, mouse, accessories",
    "price": 29.99,
    "discount_percentage": 5.00,
    "stock": 50,
    "quantity": 10,
    "rating": 4.5,
    "availability_status": "In Stock",
    "thumbnail": "https://images.unsplash.com/photo-xxx",
    "category_id": 1
}
```

**GET all products response:**
```json
{
    "success": true,
    "total": 1,
    "skip": 0,
    "limit": 10,
    "products": [
        {
            "product_id": 1,
            "product_name": "Hacking Laptop",
            "price": "1500.00",
            "category_name": "Laptops",
            "images": [...],
            "reviews": [...]
        }
    ]
}
```

---

### 🖼️ Product Images
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/product_images.php?product_id=1` | Get all images for a product |
| POST | `/api/product_images.php` | Add an image to a product |
| DELETE | `/api/product_images.php?id=1` | Delete an image |

**POST body:**
```json
{
    "image_url": "https://images.unsplash.com/photo-xxx",
    "is_thumbnail": 0,
    "product_id": 1
}
```

---

### ⭐ Reviews
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/reviews.php?product_id=1` | Get all reviews for a product |
| POST | `/api/reviews.php` | Add a review to a product |
| DELETE | `/api/reviews.php?id=1` | Delete a review |

**POST body:**
```json
{
    "rating": 5,
    "comment": "Great product!",
    "reviewer_name": "Bodaro",
    "reviewer_email": "bodaro@gmail.com",
    "product_id": 1
}
```

---

## 🗄️ Database Schema

```
categories
    |___ products
              |___ product_images
              |___ reviews
```

| Table | Description |
|-------|-------------|
| `categories` | Product categories with auto-generated URL |
| `products` | Main product table with price, stock, rating etc. |
| `product_images` | Multiple images per product |
| `reviews` | Customer reviews per product |

---

## 📮 Postman Collection
Import `WebAPI_Final.postman_collection.json` into Postman to get all endpoints ready to test.

---

## 👤 Author
**Bodaro** — MIS Students
