#!/usr/bin/env sh

echo "Deleting output files older than 1 hour..."
find /app/public/output -type f -mmin +60
find /app/public/output -type f -mmin +60 -exec rm -f {} \;
