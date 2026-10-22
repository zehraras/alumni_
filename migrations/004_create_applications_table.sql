-- Migration: 004_create_applications_table.sql
-- Description: Create applications table with foreign key relations to job_postings and users

CREATE TABLE IF NOT EXISTS applications (
    id SERIAL PRIMARY KEY,
    job_posting_id INTEGER NOT NULL REFERENCES job_postings(id) ON DELETE CASCADE,
    user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
    applicant_name VARCHAR(150) NOT NULL,
    applicant_email VARCHAR(150) NOT NULL,
    resume_url VARCHAR(255),
    cover_letter TEXT,
    status VARCHAR(50) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_applications_job_posting_id ON applications(job_posting_id);
CREATE INDEX IF NOT EXISTS idx_applications_user_id ON applications(user_id);
