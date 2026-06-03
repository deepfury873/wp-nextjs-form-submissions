# PowerShell setup helper for Windows
# Docker Compose writes progress lines to stderr; avoid Stop so those are not fatal.
$ErrorActionPreference = 'SilentlyContinue'

function Invoke-WpCli {
    param(
        [Parameter(Mandatory = $true, ValueFromRemainingArguments = $true)]
        [string[]]$Args
    )

    $raw = docker compose run --rm wp-cli @Args --path=/var/www/html 2>&1
    $exitCode = $LASTEXITCODE

    $lines = [System.Collections.Generic.List[string]]::new()
    foreach ($line in $raw) {
        if ($line -is [System.Management.Automation.ErrorRecord]) {
            $text = $line.ToString()
            if ($text -match '^ Container ') { continue }
            $lines.Add($text)
        }
        else {
            $lines.Add([string]$line)
        }
    }

    return [PSCustomObject]@{
        ExitCode = $exitCode
        Output   = $lines
    }
}

function Get-WpCliStdout {
    param($Result)
    ($Result.Output | Where-Object { $_ -and $_ -notmatch '^\s*$' }) -join "`n"
}

Write-Host "Starting Docker services..."
docker compose up -d | Out-Null
if ($LASTEXITCODE -ne 0) {
    Write-Error "docker compose up failed (exit $LASTEXITCODE)"
    exit 1
}

Write-Host "Waiting for WordPress (30s)..."
Start-Sleep -Seconds 30

Write-Host "Checking WordPress installation..."
$check = Invoke-WpCli -Args @('core', 'is-installed')
if ($check.ExitCode -ne 0) {
    Write-Host "Installing WordPress..."
    $install = Invoke-WpCli -Args @(
        'core', 'install',
        '--url=http://localhost:8080',
        '--title=Lead Capture',
        '--admin_user=admin',
        '--admin_password=admin123!',
        '--admin_email=admin@example.com',
        '--skip-email'
    )
    if ($install.ExitCode -ne 0) {
        Write-Host (Get-WpCliStdout $install)
        Write-Error "WordPress install failed (exit $($install.ExitCode))"
        exit 1
    }
}

$activatePlugin = Invoke-WpCli -Args @('plugin', 'activate', 'lead-capture')
if ($activatePlugin.ExitCode -ne 0) {
    Write-Host (Get-WpCliStdout $activatePlugin)
    Write-Error "Failed to activate lead-capture plugin"
    exit 1
}

$activateTheme = Invoke-WpCli -Args @('theme', 'activate', 'lead-capture-theme')
if ($activateTheme.ExitCode -ne 0) {
    Write-Host (Get-WpCliStdout $activateTheme)
    Write-Error "Failed to activate lead-capture-theme"
    exit 1
}

$createPage = Invoke-WpCli -Args @(
    'post', 'create',
    '--post_type=page',
    '--post_title=Application',
    '--post_status=publish',
    '--porcelain'
)
if ($createPage.ExitCode -ne 0) {
    Write-Host (Get-WpCliStdout $createPage)
    Write-Error "Failed to create Application page"
    exit 1
}
$pageId = ($createPage.Output | Where-Object { $_ -match '^\d+$' } | Select-Object -Last 1)

$meta = Invoke-WpCli -Args @('post', 'meta', 'update', $pageId, '_wp_page_template', 'template-application.php')
if ($meta.ExitCode -ne 0) {
    Write-Host (Get-WpCliStdout $meta)
    Write-Error "Failed to set page template"
    exit 1
}

$flush = Invoke-WpCli -Args @('rewrite', 'flush')
if ($flush.ExitCode -ne 0) {
    Write-Host (Get-WpCliStdout $flush)
    Write-Error "Failed to flush rewrite rules"
    exit 1
}

Write-Host "Form URL: http://localhost:8080/?page_id=$pageId"
Write-Host "Admin: http://localhost:8080/wp-admin (admin / admin123!)"
