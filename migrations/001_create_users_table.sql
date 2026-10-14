-- Migration: 001_create_users_table.sql
-- Description: Create users table for alumni members

CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    isim VARCHAR(100) NOT NULL,
    soyisim VARCHAR(100),
    bolum VARCHAR(150),
    mezuniyet_yili INTEGER,
    sehir VARCHAR(100),
    pozisyon VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP
);
