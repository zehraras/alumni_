-- Migration: 003_create_job_postings_table.sql
-- Description: Create job_postings table with foreign key relation to users

CREATE TABLE IF NOT EXISTS job_postings (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    title VARCHAR(255) NOT NULL,
    company VARCHAR(255) NOT NULL,
    location VARCHAR(150),
    description TEXT NOT NULL,
    requirements TEXT,
    salary_range VARCHAR(100),
    job_type VARCHAR(50) DEFAULT 'Full-time',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_job_postings_user_id ON job_postings(user_id);
