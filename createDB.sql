-- Create the categories table
CREATE TABLE categories (
    id   TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(60) UNIQUE NOT NULL
);

-- Create the products table
CREATE TABLE products (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    name        VARCHAR(60) NOT NULL,
    brand       VARCHAR(60) NOT NULL,
    category_id TINYINT UNSIGNED NOT NULL,
    description TEXT,
    image_url   VARCHAR(255),
    base_price  DECIMAL(6,2) UNSIGNED NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Create the product_sizes table (Handles different sizes & stock)
CREATE TABLE product_sizes (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    product_id  INT NOT NULL,
    size        DECIMAL(4,1) NOT NULL,
    stock       INT NOT NULL CHECK (stock >= 0),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE (product_id, size) -- Ensures no duplicate sizes per product
);

-- Create the discounts table
CREATE TABLE discounts (
    id            INT PRIMARY KEY AUTO_INCREMENT,
    product_id    INT NOT NULL,
    discount_type ENUM('percentage', 'fixed') NOT NULL,
    value         DECIMAL(5,2) NOT NULL, -- Supports up to 100.00%
    start_date    DATETIME DEFAULT CURRENT_TIMESTAMP,
    end_date      DATETIME NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Create the users table
CREATE TABLE users (
    id           INT PRIMARY KEY AUTO_INCREMENT,
    name         VARCHAR(100) NOT NULL,
    email        VARCHAR(255) UNIQUE NOT NULL,
    password     VARCHAR(255) NOT NULL,
    profile_pic  BLOB, -- Stores profile pictures
    address      TEXT,
    user_type    ENUM('user', 'employee', 'admin') NOT NULL DEFAULT 'user',
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create the orders table
CREATE TABLE orders (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    user_id     INT NOT NULL,
    total_price DECIMAL(6,2) NOT NULL,
    status      ENUM('pending', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create the order_items table
CREATE TABLE order_items (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    order_id    INT NOT NULL,
    product_id  INT NOT NULL,
    size        DECIMAL(4,1) UNSIGNED NOT NULL,
    quantity    INT NOT NULL CHECK (quantity > 0),
    price       DECIMAL(6,2) UNSIGNED NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Create the reviews table
CREATE TABLE reviews (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    product_id  INT NOT NULL,
    user_id     INT NOT NULL,
    title       VARCHAR(100) NOT NULL, -- Title for the review
    rating      TINYINT UNSIGNED NOT NULL CHECK (rating BETWEEN 1 AND 5), -- Rating from 1 to 5 stars
    review_text TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE (product_id, user_id) -- Ensures one review per user per shoe
);

-- Create the store_locations table
CREATE TABLE store_locations (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    name        VARCHAR(100) NOT NULL, -- Store name or branch
    address     TEXT NOT NULL, -- Full address
    country     VARCHAR(60) NOT NULL,
    zip_code    VARCHAR(20),
    phone       VARCHAR(20),
    latitude    DECIMAL(9,6), -- For maps integration
    longitude   DECIMAL(9,6),
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create the reset_password table
CREATE TABLE reset_password (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    user_id     INT NOT NULL,
    token       VARCHAR(255) UNIQUE NOT NULL, -- Reset token (store as hash for security)
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Token generation time
    expires_at  DATETIME NOT NULL, -- Will be set automatically in trigger
    used        TINYINT(1) NOT NULL DEFAULT 0 CHECK (used IN (0,1)), -- Ensures only 0 or 1
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create trigger to set expires_at automatically
DELIMITER //

CREATE TRIGGER set_expires_at
BEFORE INSERT ON reset_password
FOR EACH ROW
BEGIN
    SET NEW.expires_at = NOW() + INTERVAL 1 HOUR; -- one hour to reset password before it expires
END;

//

DELIMITER ;
