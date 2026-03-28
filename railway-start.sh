#!/bin/bash

# Run database migrations
php artisan migrate --force

# Link storage
php artisan storage:link

# Start Laravel
php artisan serve --host=0.0.0.0 --port=8000