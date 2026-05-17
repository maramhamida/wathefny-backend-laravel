FROM php:8.2-cli

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    unzip zip curl git libpng-dev libonig-dev libxml2-dev

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# نسخ المشروع أولاً
COPY . /var/www

# تأكد أن composer.json موجود ثم install
RUN composer install --no-interaction --no-dev --prefer-dist

EXPOSE 10000

CMD php artisan serve --host=0.0.0.0 --port=$PORT