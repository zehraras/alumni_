-- Migration: 005_add_auth_and_roles_to_users.sql
-- Description: Add authentication (email, password_hash, auth_token) and authorization (role) to users

ALTER TABLE users ADD COLUMN IF NOT EXISTS email VARCHAR(150);
ALTER TABLE users ADD COLUMN IF NOT EXISTS password_hash VARCHAR(255);
ALTER TABLE users ADD COLUMN IF NOT EXISTS role VARCHAR(50) DEFAULT 'alumni';
ALTER TABLE users ADD COLUMN IF NOT EXISTS auth_token VARCHAR(255);

-- Ensure email is unique when present
CREATE UNIQUE INDEX IF NOT EXISTS idx_users_email ON users(email);
