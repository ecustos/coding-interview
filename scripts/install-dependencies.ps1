#requires -Version 5.1

[CmdletBinding()]
param(
    [switch] $SkipSystemInstall
)

$ErrorActionPreference = 'Stop'

$RootDir = Resolve-Path (Join-Path $PSScriptRoot '..')
$PhpMinVersion = [version]'8.3.0'
$NodeMinMajor = 20

function Write-Step {
    param([string] $Message)
    Write-Host "==> $Message" -ForegroundColor Cyan
}

function Write-WarningMessage {
    param([string] $Message)
    Write-Host "Warning: $Message" -ForegroundColor Yellow
}

function Test-Command {
    param([string] $Command)
    return $null -ne (Get-Command $Command -ErrorAction SilentlyContinue)
}

function Refresh-Path {
    $machinePath = [Environment]::GetEnvironmentVariable('Path', 'Machine')
    $userPath = [Environment]::GetEnvironmentVariable('Path', 'User')
    $env:Path = "$machinePath;$userPath"
}

function Install-WingetPackage {
    param(
        [string] $Command,
        [string] $PackageId,
        [string] $PackageName
    )

    if (Test-Command $Command) {
        return
    }

    if (-not (Test-Command winget)) {
        throw "$PackageName was not found and winget is not available. Install it manually or rerun with -SkipSystemInstall after installing the requirements."
    }

    Write-Step "Installing $PackageName with winget..."
    winget install --id $PackageId --exact --source winget --accept-package-agreements --accept-source-agreements
    Refresh-Path
}

function Install-SystemDependencies {
    if ($SkipSystemInstall) {
        Write-WarningMessage 'Skipping system package installation because -SkipSystemInstall was provided.'
        return
    }

    Install-WingetPackage -Command 'php' -PackageId 'PHP.PHP.8.3' -PackageName 'PHP 8.3'
    Install-WingetPackage -Command 'composer' -PackageId 'Composer.Composer' -PackageName 'Composer'
    Install-WingetPackage -Command 'node' -PackageId 'OpenJS.NodeJS.LTS' -PackageName 'Node.js LTS'
    Install-WingetPackage -Command 'sqlite3' -PackageId 'SQLite.SQLite' -PackageName 'SQLite'
}

function Get-PhpIniPath {
    $iniLine = php --ini | Select-String -Pattern 'Loaded Configuration File' | Select-Object -First 1

    if (-not $iniLine) {
        return $null
    }

    $path = ($iniLine.ToString() -split ':\s+', 2)[1].Trim()

    if ($path -eq '(none)' -or [string]::IsNullOrWhiteSpace($path)) {
        return $null
    }

    return $path
}

function Enable-PhpExtension {
    param([string] $Extension)

    php -r "exit(extension_loaded('$Extension') ? 0 : 1);"
    if ($LASTEXITCODE -eq 0) {
        return
    }

    $phpIni = Get-PhpIniPath
    if (-not $phpIni -or -not (Test-Path $phpIni)) {
        throw "The PHP $Extension extension is required, but no loaded php.ini file was found."
    }

    Write-Step "Enabling PHP extension $Extension in $phpIni..."
    $content = Get-Content $phpIni -Raw
    $pattern = "(?m)^\s*;\s*extension=$([regex]::Escape($Extension))\s*$"

    if ($content -notmatch $pattern) {
        throw "The PHP $Extension extension is required, but it was not found in $phpIni."
    }

    $content = [regex]::Replace($content, $pattern, "extension=$Extension")
    Set-Content -Path $phpIni -Value $content -Encoding ASCII

    php -r "exit(extension_loaded('$Extension') ? 0 : 1);"
    if ($LASTEXITCODE -ne 0) {
        throw "PHP extension $Extension is still not loaded after updating php.ini."
    }
}

function Assert-Php {
    if (-not (Test-Command php)) {
        throw 'PHP was not found in PATH.'
    }

    $phpVersion = [version](php -r 'echo PHP_VERSION;')
    if ($phpVersion -lt $PhpMinVersion) {
        throw "PHP $PhpMinVersion+ is required. Current version: $phpVersion"
    }

    Enable-PhpExtension -Extension 'pdo_sqlite'
}

function Assert-Node {
    if (-not (Test-Command node)) {
        throw 'Node.js was not found in PATH.'
    }

    if (-not (Test-Command npm)) {
        throw 'npm was not found in PATH.'
    }

    $nodeMajor = [int](node -p "Number(process.versions.node.split('.')[0])")
    if ($nodeMajor -lt $NodeMinMajor) {
        throw "Node.js $NodeMinMajor+ is required. Current version: $(node --version)"
    }
}

function Assert-Commands {
    Refresh-Path
    Assert-Php
    Assert-Node

    if (-not (Test-Command composer)) {
        throw 'Composer was not found in PATH.'
    }

    if (-not (Test-Command sqlite3)) {
        throw 'SQLite CLI was not found in PATH.'
    }
}

function Install-ProjectDependencies {
    Push-Location $RootDir
    try {
        Write-Step 'Installing PHP dependencies...'
        composer install

        if (Test-Path 'package.json') {
            Write-Step 'Installing Node dependencies...'
            npm install
        }
    } finally {
        Pop-Location
    }
}

function Initialize-LaravelFiles {
    Push-Location $RootDir
    try {
        if (-not (Test-Path '.env')) {
            Write-Step 'Creating .env from .env.example...'
            Copy-Item '.env.example' '.env'
        }

        if (-not (Test-Path 'database')) {
            New-Item -ItemType Directory -Path 'database' | Out-Null
        }

        if (-not (Test-Path 'database/database.sqlite')) {
            Write-Step 'Creating SQLite database file...'
            New-Item -ItemType File -Path 'database/database.sqlite' | Out-Null
        }

        $envContent = Get-Content '.env' -Raw
        if ($envContent -notmatch '(?m)^APP_KEY=base64:') {
            Write-Step 'Generating Laravel APP_KEY...'
            php artisan key:generate --ansi
        }
    } finally {
        Pop-Location
    }
}

Write-Step 'Preparing dependencies for Mini eCustos...'
Install-SystemDependencies
Assert-Commands
Install-ProjectDependencies
Initialize-LaravelFiles
Write-Step "Done. Run 'php artisan serve' or use WSL/Git Bash with 'make dev' to start the API."
