-- PostgreSQL/Supabase Database Schema
-- Run this script in your Supabase SQL editor to create all necessary tables

-- Users table (quoted because "user" is a reserved word in PostgreSQL)
CREATE TABLE "user" (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) UNIQUE NOT NULL,
    address TEXT,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin table
CREATE TABLE admin (
    id SERIAL PRIMARY KEY,
    phone VARCHAR(20) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Pilot table
CREATE TABLE pilot (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) UNIQUE NOT NULL,
    address TEXT,
    password VARCHAR(255) NOT NULL,
    pilot_license_number VARCHAR(100),
    dgca_license_photo VARCHAR(255),
    aadhaar_number VARCHAR(20),
    address_photo VARCHAR(255),
    person_photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders table
CREATE TABLE orders (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    service_type VARCHAR(100) NOT NULL,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    hours INTEGER NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    additional_msg TEXT,
    transaction_id VARCHAR(100),
    payment_screenshot VARCHAR(255),
    order_status VARCHAR(50) DEFAULT 'Pending Admin Approval',
    pilot_phone VARCHAR(20),
    cancellation_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Successful orders table
CREATE TABLE userordersuccess (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    service_type VARCHAR(100) NOT NULL,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    hours INTEGER NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    additional_msg TEXT,
    rating INTEGER CHECK (rating >= 1 AND rating <= 5),
    review TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cancelled orders table
CREATE TABLE userordercancel (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    service_type VARCHAR(100) NOT NULL,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    hours INTEGER NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    additional_msg TEXT,
    refund_status VARCHAR(50) DEFAULT 'Pending',
    refund_utr VARCHAR(100),
    cancellation_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Wallet table
CREATE TABLE wallet (
    id SERIAL PRIMARY KEY,
    phone VARCHAR(20) NOT NULL,
    date DATE NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    utr VARCHAR(100),
    additional_msg TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- User wallet table (for pending transactions)
CREATE TABLE userwallet (
    id SERIAL PRIMARY KEY,
    user_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    txn_id VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Pilot earnings table
CREATE TABLE pilot_earnings (
    id SERIAL PRIMARY KEY,
    order_id INTEGER,
    pilot_phone VARCHAR(20) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add foreign key constraints
ALTER TABLE orders ADD CONSTRAINT fk_orders_pilot FOREIGN KEY (pilot_phone) REFERENCES pilot(phone);
ALTER TABLE pilot_earnings ADD CONSTRAINT fk_earnings_pilot FOREIGN KEY (pilot_phone) REFERENCES pilot(phone);
ALTER TABLE pilot_earnings ADD CONSTRAINT fk_earnings_order FOREIGN KEY (order_id) REFERENCES userordersuccess(id);

-- Create indexes for better performance
CREATE INDEX idx_user_phone ON "user"(phone);
CREATE INDEX idx_admin_phone ON admin(phone);
CREATE INDEX idx_pilot_phone ON pilot(phone);
CREATE INDEX idx_orders_phone ON orders(phone);
CREATE INDEX idx_orders_status ON orders(order_status);
CREATE INDEX idx_orders_pilot_phone ON orders(pilot_phone);
CREATE INDEX idx_userordersuccess_phone ON userordersuccess(phone);
CREATE INDEX idx_userordercancel_phone ON userordercancel(phone);
CREATE INDEX idx_wallet_phone ON wallet(phone);
CREATE INDEX idx_pilot_earnings_pilot_phone ON pilot_earnings(pilot_phone);

