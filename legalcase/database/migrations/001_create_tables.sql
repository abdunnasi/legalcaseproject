-- =============================================
-- LegalCase Pro - Database Migration
-- Run this file to set up the database schema
-- =============================================

CREATE DATABASE IF NOT EXISTS legalcase_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE legalcase_db;

-- ----------------------
-- USERS TABLE
-- ----------------------
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(150) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    role        ENUM('admin','lawyer','clerk','staff') NOT NULL DEFAULT 'clerk',
    phone       VARCHAR(30),
    is_active   TINYINT(1) NOT NULL DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ----------------------
-- CLIENTS TABLE
-- ----------------------
CREATE TABLE IF NOT EXISTS clients (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    full_name     VARCHAR(150) NOT NULL,
    email         VARCHAR(150),
    phone         VARCHAR(30),
    address       TEXT,
    id_number     VARCHAR(50),
    date_of_birth DATE,
    notes         TEXT,
    created_by    INT,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- ----------------------
-- CASES TABLE
-- ----------------------
CREATE TABLE IF NOT EXISTS cases (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    case_number     VARCHAR(50) NOT NULL UNIQUE,
    title           VARCHAR(255) NOT NULL,
    case_type       ENUM('criminal','civil','family','corporate','land','other') NOT NULL,
    status          ENUM('filed','under_investigation','hearing_scheduled','in_progress','closed','dismissed') NOT NULL DEFAULT 'filed',
    description     TEXT,
    lawyer_id       INT,
    client_id       INT,
    court_name      VARCHAR(150),
    judge_name      VARCHAR(150),
    filing_date     DATE,
    closing_date    DATE,
    created_by      INT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lawyer_id)  REFERENCES users(id)   ON DELETE SET NULL,
    FOREIGN KEY (client_id)  REFERENCES clients(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id)   ON DELETE SET NULL
);

-- ----------------------
-- CASE NOTES / PROGRESS
-- ----------------------
CREATE TABLE IF NOT EXISTS case_notes (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    case_id    INT NOT NULL,
    user_id    INT,
    note       TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (case_id) REFERENCES cases(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ----------------------
-- DOCUMENTS TABLE
-- ----------------------
CREATE TABLE IF NOT EXISTS documents (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    case_id       INT NOT NULL,
    title         VARCHAR(255) NOT NULL,
    doc_type      ENUM('evidence','affidavit','court_ruling','contract','petition','other') NOT NULL DEFAULT 'other',
    file_name     VARCHAR(255) NOT NULL,
    file_path     VARCHAR(500) NOT NULL,
    file_size     INT,
    mime_type     VARCHAR(100),
    uploaded_by   INT,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (case_id)     REFERENCES cases(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
);

-- ----------------------
-- COURT HEARINGS / SCHEDULE
-- ----------------------
CREATE TABLE IF NOT EXISTS hearings (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    case_id      INT NOT NULL,
    title        VARCHAR(255) NOT NULL,
    hearing_date DATE NOT NULL,
    hearing_time TIME,
    court_room   VARCHAR(100),
    court_name   VARCHAR(150),
    judge_name   VARCHAR(150),
    status       ENUM('scheduled','completed','postponed','cancelled') NOT NULL DEFAULT 'scheduled',
    notes        TEXT,
    created_by   INT,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (case_id)    REFERENCES cases(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- ----------------------
-- NOTIFICATIONS TABLE
-- ----------------------
CREATE TABLE IF NOT EXISTS notifications (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    title      VARCHAR(255) NOT NULL,
    message    TEXT NOT NULL,
    type       ENUM('hearing','case_update','document','system') DEFAULT 'system',
    is_read    TINYINT(1) DEFAULT 0,
    link       VARCHAR(300),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ----------------------
-- AUDIT LOG TABLE
-- ----------------------
CREATE TABLE IF NOT EXISTS audit_logs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT,
    action      VARCHAR(100) NOT NULL,
    table_name  VARCHAR(100),
    record_id   INT,
    description TEXT,
    ip_address  VARCHAR(50),
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- =============================================
-- SEED: Default admin user
-- Password: Admin@1234 (bcrypt hashed)
-- =============================================
INSERT INTO users (name, email, password, role) VALUES
('System Administrator', 'admin@legalcase.ug', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Jane Nakato', 'lawyer@legalcase.ug', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'lawyer'),
('Paul Ssemakula', 'clerk@legalcase.ug', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'clerk');
-- Default password for all seed users: password
