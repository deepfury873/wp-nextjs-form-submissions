#!/usr/bin/env sh
set -e

OUT_DIR="docs/lighthouse"
mkdir -p "$OUT_DIR"

if ! command -v lighthouse >/dev/null 2>&1; then
  echo "Install Lighthouse: npm install -g lighthouse"
  exit 1
fi

WP_URL="${WP_URL:-http://localhost:8080}"
NEXT_URL="${NEXT_URL:-http://localhost:3000}"

lighthouse "$WP_URL" --output html --output json --output-path "$OUT_DIR/wordpress" --chrome-flags="--headless" --only-categories=performance,accessibility,best-practices,seo
lighthouse "$NEXT_URL" --output html --output json --output-path "$OUT_DIR/nextjs" --chrome-flags="--headless" --only-categories=performance,accessibility,best-practices,seo

echo "Reports saved to $OUT_DIR"
