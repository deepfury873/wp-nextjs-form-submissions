$OutDir = "docs/lighthouse"
New-Item -ItemType Directory -Force -Path $OutDir | Out-Null

$wpUrl = if ($env:WP_URL) { $env:WP_URL } else { "http://localhost:8080" }
$nextUrl = if ($env:NEXT_URL) { $env:NEXT_URL } else { "http://localhost:3000" }

if (-not (Get-Command lighthouse -ErrorAction SilentlyContinue)) {
  Write-Host "Install Lighthouse: npm install -g lighthouse"
  exit 1
}

lighthouse $wpUrl --output html --output json --output-path "$OutDir/wordpress" --chrome-flags="--headless" --only-categories=performance,accessibility,best-practices,seo
lighthouse $nextUrl --output html --output json --output-path "$OutDir/nextjs" --chrome-flags="--headless" --only-categories=performance,accessibility,best-practices,seo

Write-Host "Reports saved to $OutDir"
