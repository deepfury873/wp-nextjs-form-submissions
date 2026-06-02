#!/usr/bin/env sh
set -e

echo "Waiting for WordPress..."
sleep 10

docker compose run --rm wp-cli core is-installed --path=/var/www/html || \
  docker compose run --rm wp-cli core install \
    --path=/var/www/html \
    --url="http://localhost:8080" \
    --title="Lead Capture" \
    --admin_user="admin" \
    --admin_password="admin123!" \
    --admin_email="admin@example.com" \
    --skip-email

docker compose run --rm wp-cli plugin activate lead-capture --path=/var/www/html
docker compose run --rm wp-cli theme activate lead-capture-theme --path=/var/www/html

PAGE_ID=$(docker compose run --rm wp-cli post create \
  --path=/var/www/html \
  --post_type=page \
  --post_title="Application" \
  --post_status=publish \
  --porcelain)

docker compose run --rm wp-cli post meta update "$PAGE_ID" _wp_page_template template-application.php --path=/var/www/html
docker compose run --rm wp-cli rewrite flush --path=/var/www/html

echo "Done. Form: http://localhost:8080/?page_id=$PAGE_ID"
echo "Admin: http://localhost:8080/wp-admin (admin / admin123!)"
