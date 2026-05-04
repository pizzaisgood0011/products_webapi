CREATE DATABASE IF NOT EXISTS webfinal_db;
USE webfinal_db;

-- create tables
CREATE TABLE IF NOT EXISTS categories(
	category_id int PRIMARY KEY AUTO_INCREMENT,
	category_name varchar(50) NOT NULL,
	slug varchar(50) NOT NULL UNIQUE ,
	url varchar(255) 
	GENERATED ALWAYS AS (
		CONCAT('http://localhost/api/products/category/', slug)					
	) STORED,
	created_at timestamp DEFAULT CURRENT_TIMESTAMP()
);

CREATE TABLE IF NOT EXISTS products(
	product_id int PRIMARY KEY AUTO_INCREMENT,
	product_name varchar(100) NOT NULL ,
	product_title varchar(100) NOT NULL,
	SKU varchar(50) UNIQUE NOT NULL ,
	brand varchar(50),
	description varchar(500),
	tags varchar(255),
	price decimal(20, 2) check(price>0) NOT NULL,
	discount_percentage decimal(5, 2) DEFAULT 0,
	stock int DEFAULT 0,
	quantity int DEFAULT 0,
	amount decimal(20, 2) GENERATED ALWAYS AS(price*quantity) STORED,
	rating decimal(10, 2) check(rating >=1 AND rating <=5),
	availability_status varchar(50) DEFAULT 'In Stock',
	thumbnail varchar(2048),
	created_at timestamp DEFAULT CURRENT_TIMESTAMP(),
	updated_at timestamp DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
	category_id int,
	CONSTRAINT fk_category FOREIGN KEY (category_id) 
	REFERENCES categories(category_id)
	ON DELETE SET NULL
	ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS product_images(
	image_id int PRIMARY KEY AUTO_INCREMENT,
	image_url varchar(2048) NOT NULL,
	is_thumbnail bool DEFAULT FALSE,
	created_at timestamp DEFAULT CURRENT_TIMESTAMP(),
	product_id int NOT NULL,
	CONSTRAINT fk_image_product FOREIGN KEY(product_id)
	REFERENCES products(product_id)
	ON DELETE CASCADE
	ON UPDATE CASCADE
);

CREATE TABLE reviews (
    review_id int PRIMARY KEY AUTO_INCREMENT,
    rating decimal(3, 2) NOT NULL
    CHECK (rating >= 1 AND rating <= 5),
    comment varchar(500),
    reviewer_name varchar(100) NOT NULL,
    reviewer_email varchar(100) NOT NULL,
    created_at timestamp  DEFAULT CURRENT_TIMESTAMP(),
    product_id int NOT NULL,
    CONSTRAINT fk_review_product FOREIGN KEY (product_id) 
    REFERENCES products(product_id)
	ON DELETE CASCADE
    ON UPDATE CASCADE
);

-- insert data
-- insert categories data
INSERT INTO categories (category_name , slug)
VALUES ('Laptops', 'laptops'), ('Smartphones', 'smartphones');

-- insert products data
INSERT INTO products(
	product_name, product_title, SKU, brand, description, tags,price,
	discount_percentage, stock, quantity, rating, category_id) 
VALUES ('Hacking Laptop', 'Hacking Laptop Beginner',
		'HKI-LP-001', 'Bodaro', 'Best laptop all the time', 'laptop, technology',
		1500.00, 5.10, 20, 5, 4.50, 1);
UPDATE products 
SET thumbnail = 'https://images.unsplash.com/photo-1605134513573-384dcf99a44c?q=80&w=1170&auto=format&fit=crop'
WHERE product_id = 1;

-- insert product image data
INSERT INTO product_images (image_url, is_thumbnail, product_id)
VALUES (
	'https://images.unsplash.com/photo-1605134513573-384dcf99a44c?q=80&w=1170&auto=format&fit=crop',
	TRUE, 1);


-- insert reviews data
INSERT INTO reviews(rating, comment, reviewer_name , reviewer_email, product_id)
VALUES (4, 'Recommend!', 'Sang Chhenghok', 'hokhok@gmail.com', 1),
		(5, 'Best Performance', 'Seb Kimtheng', 'sentheng@gmail.com', 1);

SELECT * FROM categories;
SELECT * FROM products;
SELECT * FROM product_images;
SELECT * FROM reviews;