# Generating Lighthouse reports

Lighthouse must be run on your machine after both frontends are up.

## Steps

1. Start WordPress: `docker compose up -d` then `.\scripts\wp-setup.ps1`
2. Start Next.js: `cd nextjs && npm install && npm run dev`
3. Install Lighthouse: `npm install -g lighthouse`
4. Run: `.\scripts\lighthouse.ps1` (Windows) or `./scripts/lighthouse.sh` (Unix)

## Expected output files

After running, commit these files:

- `wordpress.report.html` / `wordpress.report.json`
- `nextjs.report.html` / `nextjs.report.json`

## Recording scores in SUMMARY.md

After generation, note the category scores in `SUMMARY.md` for quick reviewer reference.
