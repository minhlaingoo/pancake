# Variables
PHP=php
COMPOSER=composer
NPM=npm
ARTISAN=php artisan

# Default target
all: setup

# Install PHP dependencies
install:
	$(COMPOSER) install

# Install NPM dependencies
npm-install:
	$(NPM) install

# Generate Laravel application key
key:
	$(ARTISAN) key:generate

# Run database migrations
migrate:
	$(ARTISAN) migrate

# Seed the database
seed:
	$(ARTISAN) db:seed

# Compile assets for development
dev:
	$(NPM) run dev

# Compile assets for production
prod:
	$(NPM) run prod

# Watch assets for changes
watch:
	$(NPM) run watch

# Start the Laravel development server
serve:
	$(ARTISAN) serve

# Clear application cache
clear:
	$(ARTISAN) cache:clear
	$(ARTISAN) config:clear
	$(ARTISAN) route:clear
	$(ARTISAN) view:clear

# Full setup: install dependencies, generate key, run migrations, and compile assets
setup: install npm-install key migrate seed

# Clean up installed dependencies
clean:
	rm -rf vendor/
	rm -rf node_modules/


# Help command to display available targets
help:
	@echo "Available targets:"
	@echo "  all         - Run the full setup (install dependencies, generate key, migrate, compile assets)"
	@echo "  install     - Install PHP dependencies using Composer"
	@echo "  npm-install - Install NPM dependencies"
	@echo "  key         - Generate Laravel application key"
	@echo "  migrate     - Run database migrations"
	@echo "  seed        - Seed the database"
	@echo "  dev         - Compile assets for development"
	@echo "  prod        - Compile assets for production"
	@echo "  watch       - Watch assets for changes"
	@echo "  serve       - Start the Laravel development server"
	@echo "  clear       - Clear application cache"
	@echo "  setup       - Full setup (install dependencies, generate key, migrate, compile assets)"
	@echo "  clean       - Clean up installed dependencies"
	@echo "  help        - Display this help message"

.PHONY: all install npm-install key migrate seed dev prod watch serve clear setup clean test help
