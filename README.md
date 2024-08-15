# Installation

- composer install
- php artisan shop:install
- make .env. and .env.testing from .env.example

[//]: # (- php artisan storage:link)

[//]: # (- php artisan migrate)

# Deploy

# NPM

Устанавливаем node (если не установила через докер файл)
- apt-get update
- curl -sL https://deb.nodesource.com/setup_20.x | bash -
- apt-get install -y nodejs

- npm install
- npm install -D tailwindcss
- npm install @tailwindcss/line-clamp autoprefixer postcss

vite build
… export PATH=$PATH:/var/www/node_modules/.bin


