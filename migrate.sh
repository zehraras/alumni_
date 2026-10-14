#!/bin/bash
# Run database migrations via Docker PostgreSQL container

set -e

echo "=== Running Database Migrations via Docker ==="

for file in migrations/*.sql; do
    echo "Running migration: $(basename "$file") ..."
    docker exec -i alumni_postgres psql -U alumni_user -d alumni_db < "$file"
done

echo ""
echo "All migrations applied successfully!"
docker exec -i alumni_postgres psql -U alumni_user -d alumni_db -c "\dt"
