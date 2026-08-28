#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PHP_MIN_VERSION="8.3.0"
NODE_MIN_MAJOR="20"

info() {
    printf '\033[1;34m==>\033[0m %s\n' "$1"
}

warn() {
    printf '\033[1;33mWarning:\033[0m %s\n' "$1"
}

fail() {
    printf '\033[1;31mError:\033[0m %s\n' "$1" >&2
    exit 1
}

command_exists() {
    command -v "$1" >/dev/null 2>&1
}

sudo_if_needed() {
    if [ "$(id -u)" -eq 0 ]; then
        "$@"
    else
        sudo "$@"
    fi
}

install_with_brew() {
    brew update
    brew install php composer node sqlite make
}

install_with_apt() {
    sudo_if_needed apt-get update
    sudo_if_needed apt-get install -y \
        php-cli php-mbstring php-xml php-sqlite3 php-curl php-zip \
        composer nodejs npm sqlite3 make unzip curl
}

install_with_dnf() {
    sudo_if_needed dnf install -y \
        php-cli php-mbstring php-xml php-pdo php-sqlite3 php-curl php-zip \
        composer nodejs npm sqlite make unzip curl
}

install_with_yum() {
    sudo_if_needed yum install -y \
        php-cli php-mbstring php-xml php-pdo php-sqlite3 php-curl php-zip \
        composer nodejs npm sqlite make unzip curl
}

install_with_pacman() {
    sudo_if_needed pacman -Sy --needed --noconfirm \
        php php-sqlite composer nodejs npm sqlite make unzip curl
}

install_system_dependencies() {
    if [ "${SKIP_SYSTEM_INSTALL:-0}" = "1" ]; then
        warn "Skipping system package installation because SKIP_SYSTEM_INSTALL=1."
        return
    fi

    if [ "$(uname -s)" = "Darwin" ]; then
        command_exists brew || fail "Homebrew was not found. Install Homebrew or rerun with SKIP_SYSTEM_INSTALL=1 after installing the requirements manually."
        info "Installing system dependencies with Homebrew..."
        install_with_brew
        return
    fi

    if command_exists apt-get; then
        info "Installing system dependencies with apt..."
        install_with_apt
    elif command_exists dnf; then
        info "Installing system dependencies with dnf..."
        install_with_dnf
    elif command_exists yum; then
        info "Installing system dependencies with yum..."
        install_with_yum
    elif command_exists pacman; then
        info "Installing system dependencies with pacman..."
        install_with_pacman
    else
        fail "No supported package manager was found. Install PHP 8.3+, Composer, Node.js ${NODE_MIN_MAJOR}+, npm, SQLite and Make manually, then rerun with SKIP_SYSTEM_INSTALL=1."
    fi
}

verify_php() {
    command_exists php || fail "PHP was not found in PATH."

    php -r "exit(version_compare(PHP_VERSION, '${PHP_MIN_VERSION}', '>=') ? 0 : 1);" \
        || fail "PHP ${PHP_MIN_VERSION}+ is required. Current version: $(php -r 'echo PHP_VERSION;')"

    php -r "exit(extension_loaded('pdo_sqlite') ? 0 : 1);" \
        || fail "The PHP pdo_sqlite extension is required."
}

verify_node() {
    command_exists node || fail "Node.js was not found in PATH."
    command_exists npm || fail "npm was not found in PATH."

    local node_major
    node_major="$(node -p "Number(process.versions.node.split('.')[0])")"

    if [ "$node_major" -lt "$NODE_MIN_MAJOR" ]; then
        fail "Node.js ${NODE_MIN_MAJOR}+ is required. Current version: $(node --version)"
    fi
}

verify_commands() {
    verify_php
    verify_node
    command_exists composer || fail "Composer was not found in PATH."
    command_exists sqlite3 || fail "SQLite CLI was not found in PATH."
    command_exists make || fail "Make was not found in PATH."
}

install_project_dependencies() {
    cd "$ROOT_DIR"

    info "Installing PHP dependencies..."
    composer install

    if [ -f package.json ]; then
        info "Installing Node dependencies..."
        npm install
    fi
}

prepare_laravel_files() {
    cd "$ROOT_DIR"

    if [ ! -f .env ]; then
        info "Creating .env from .env.example..."
        cp .env.example .env
    fi

    mkdir -p database
    if [ ! -f database/database.sqlite ]; then
        info "Creating SQLite database file..."
        touch database/database.sqlite
    fi

    if ! grep -q '^APP_KEY=base64:' .env; then
        info "Generating Laravel APP_KEY..."
        php artisan key:generate --ansi
    fi
}

main() {
    info "Preparing dependencies for Mini eCustos..."
    install_system_dependencies
    verify_commands
    install_project_dependencies
    prepare_laravel_files
    info "Done. Run 'make dev' to start the API."
}

main "$@"
