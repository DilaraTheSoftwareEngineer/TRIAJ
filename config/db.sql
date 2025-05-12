-- Veritabanını oluştur (eğer yoksa)
CREATE DATABASE IF NOT EXISTS `triaj_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `triaj_db`;

-- Önce patients tablosunu oluştur
CREATE TABLE IF NOT EXISTS `patients` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tc_no` VARCHAR(11) NOT NULL UNIQUE,
    `name` VARCHAR(50) NOT NULL,
    `surname` VARCHAR(50) NOT NULL,
    `birthdate` DATE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sonra hastalıklar tablosunu oluştur
CREATE TABLE IF NOT EXISTS `diseases` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tc_no` VARCHAR(11) NOT NULL,
    `disease_name` VARCHAR(100) NOT NULL,
    `urgency_level` ENUM('kirmizi', 'sari', 'yesil') NOT NULL,
    `description` TEXT,
    `status` ENUM('bekliyor', 'inceleniyor', 'tamamlandi') NOT NULL DEFAULT 'bekliyor',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY `tc_no` (`tc_no`),
    CONSTRAINT `disease_patient_fk` FOREIGN KEY (`tc_no`) REFERENCES `patients` (`tc_no`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Şikayetler tablosunu oluştur
CREATE TABLE IF NOT EXISTS `complaints` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tc_no` VARCHAR(11) NOT NULL,
    `complaint` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `urgency` ENUM('düşük', 'orta', 'yüksek') NOT NULL DEFAULT 'orta',
    `status` ENUM('bekliyor', 'inceleniyor', 'tamamlandi', 'iptal') NOT NULL DEFAULT 'bekliyor',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY `tc_no` (`tc_no`),
    CONSTRAINT `complaint_patient_fk` FOREIGN KEY (`tc_no`) REFERENCES `patients` (`tc_no`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin tablosu
CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tc_no` VARCHAR(11) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `name` VARCHAR(50) NOT NULL,
    `surname` VARCHAR(50) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 