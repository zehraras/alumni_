#!/bin/bash
# Seed database via Docker PostgreSQL container

set -e

echo "=== Seeding Database via Docker ==="

docker exec -i alumni_postgres psql -U alumni_user -d alumni_db < seeders/seed.sql

echo ""
echo "Database seeded successfully!"
echo "--- Users Table Count ---"
docker exec -i alumni_postgres psql -U alumni_user -d alumni_db -c "SELECT count(*) FROM users;"
echo "--- Announcements Table Count ---"
docker exec -i alumni_postgres psql -U alumni_user -d alumni_db -c "SELECT count(*) FROM announcements;"
