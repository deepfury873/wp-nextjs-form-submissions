# Lighthouse reports

Generate reports after both apps are running:

```bash
# WordPress on :8080, Next.js on :3000
./scripts/lighthouse.sh
```

Outputs:

| File | Description |
|------|-------------|
| `wordpress.report.html` / `.json` | WordPress application form page |
| `nextjs.report.html` / `.json` | Next.js application form page |

Re-run locally after starting services to refresh scores. Commit the generated HTML/JSON files when submitting the assessment.
