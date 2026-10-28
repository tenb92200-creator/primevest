-- PrimeVest Database Schema
-- Import this into MySQL / phpMyAdmin

CREATE DATABASE IF NOT EXISTS primevest CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE primevest;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    balance DECIMAL(15,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Investment Plans
CREATE TABLE plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    min_amount DECIMAL(15,2) NOT NULL,
    max_amount DECIMAL(15,2) NOT NULL,
    roi_percent DECIMAL(5,2) NOT NULL,          -- e.g. 5.00 = 5%
    duration_days INT NOT NULL,                  -- days until maturity
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- User Investments
CREATE TABLE investments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    expected_profit DECIMAL(15,2) NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES plans(id)
) ENGINE=InnoDB;

-- Transactions (deposits, withdrawals, investments)
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('deposit', 'withdrawal', 'investment', 'profit') NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    description VARCHAR(255),
    status ENUM('pending', 'completed', 'rejected') DEFAULT 'completed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Sample Investment Plans
INSERT INTO plans (name, description, min_amount, max_amount, roi_percent, duration_days) VALUES
('Starter Plan', 'Perfect for beginners. Low risk, steady returns.', 50.00, 499.00, 3.00, 7),
('Growth Plan', 'Balanced growth with attractive returns. Most popular.', 500.00, 4999.00, 8.00, 14),
('Premium Plan', 'Higher returns for serious investors.', 5000.00, 24999.00, 15.00, 30),
('Elite Plan', 'Maximum returns for high-net-worth clients.', 25000.00, 100000.00, 25.00, 60);

-- Demo user (password: password123)
-- Password hash for "password123"
INSERT INTO users (username, email, password, balance) VALUES
('demo', 'demo@primevest.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1000.00);
-- Note: The hash above is a common bcrypt for "password". We will use proper hashing in PHP.
