CREATE DATABASE IF NOT EXISTS webapi_db;
USE webapi_db;

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
