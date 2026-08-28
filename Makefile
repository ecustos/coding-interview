ENV_FILE ?= .env
DB_FILE ?= database/database.sqlite
HOST ?= 127.0.0.1
PORT ?= 8000

PHP_BIN ?= $(shell if command -v php >/dev/null 2>&1; then command -v php; elif [ -x "$$HOME/.config/herd-lite/bin/php" ]; then echo "$$HOME/.config/herd-lite/bin/php"; fi)
COMPOSER_BIN ?= $(shell if command -v composer >/dev/null 2>&1; then command -v composer; elif [ -x "$$HOME/.config/herd-lite/bin/composer" ]; then echo "$$HOME/.config/herd-lite/bin/composer"; fi)

ifneq ($(strip $(PHP_BIN)),)
export PATH := $(dir $(PHP_BIN)):$(PATH)
endif

.DEFAULT_GOAL := help

.PHONY: help check install env database app-key migrate seed fresh serve test dev

help:
	@echo "Available targets:"
	@echo "  make dev      Prepare the app, recreate the SQLite database, seed it and start the API"
	@echo "  make test     Prepare the app and run the test suite"
	@echo "  make fresh    Recreate the SQLite database with seed data"
	@echo "  make serve    Start the Laravel development server"

check:
	@if [ -z "$(PHP_BIN)" ]; then \
		echo "PHP 8.3+ was not found. Install PHP and try again."; \
		exit 1; \
	fi
	@if [ -z "$(COMPOSER_BIN)" ]; then \
		echo "Composer was not found. Install Composer and try again."; \
		exit 1; \
	fi
	@"$(PHP_BIN)" -r "exit(version_compare(PHP_VERSION, '8.3.0', '>=') ? 0 : 1);" || { \
		echo "PHP 8.3+ is required."; \
		exit 1; \
	}
	@"$(PHP_BIN)" -r "exit(extension_loaded('pdo_sqlite') ? 0 : 1);" || { \
		echo "The PHP pdo_sqlite extension is required."; \
		exit 1; \
	}

install: check
	@echo "Installing Composer dependencies..."
	@"$(COMPOSER_BIN)" install

env:
	@if [ ! -f "$(ENV_FILE)" ]; then \
		echo "Creating $(ENV_FILE)..."; \
		cp .env.example "$(ENV_FILE)"; \
	fi

database:
	@mkdir -p database
	@if [ ! -f "$(DB_FILE)" ]; then \
		echo "Creating SQLite database at $(DB_FILE)..."; \
		touch "$(DB_FILE)"; \
	fi

app-key: install env
	@if ! grep -q '^APP_KEY=base64:' "$(ENV_FILE)"; then \
		echo "Generating application key..."; \
		"$(PHP_BIN)" artisan key:generate --ansi; \
	fi

migrate: install env database app-key
	@echo "Running migrations..."
	@"$(PHP_BIN)" artisan migrate --force

seed: migrate
	@echo "Seeding database..."
	@"$(PHP_BIN)" artisan db:seed --force

fresh: install env database app-key
	@echo "Recreating database with seed data..."
	@"$(PHP_BIN)" artisan migrate:fresh --seed --force

serve: install env database app-key
	@echo "Starting API at http://$(HOST):$(PORT)/api"
	@"$(PHP_BIN)" artisan serve --host="$(HOST)" --port="$(PORT)"

test: install env database app-key
	@"$(PHP_BIN)" artisan test

dev: install env database app-key
	@echo "Preparing local development database..."
	@"$(PHP_BIN)" artisan migrate:fresh --seed --force
	@echo "Starting API at http://$(HOST):$(PORT)/api"
	@"$(PHP_BIN)" artisan serve --host="$(HOST)" --port="$(PORT)"
