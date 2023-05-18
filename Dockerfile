FROM php:8.1.17-fpm-alpine AS prod
RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Cria um usuário e grupo nginx dentro do container
RUN addgroup -g 1000 -S nginx \
    && adduser -u 1000 -D -S -G nginx nginx

# Altera o usuário e grupo padrão do PHP-FPM para nginx
RUN sed -i 's/user = www-data/user = nginx/g' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/group = www-data/group = nginx/g' /usr/local/etc/php-fpm.d/www.conf

# Altera as permissões dos arquivos para o usuário e grupo nginx
RUN chown -R nginx:nginx /var/www

FROM prod
RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install xdebug-3.1.5 \
    && docker-php-ext-enable xdebug
