FROM php:7.4-fpm-alpine

WORKDIR /application

# Install PHP modules
RUN apk add --no-cache --virtual \
        .phpize-deps $PHPIZE_DEPS \
        freetype-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        libmcrypt-dev \
        git \
        openssh-client \
        curl \
        wget \
        mysql-client \
        libtool \
        zlib-dev \
        icu-dev \
        libzip-dev \
        g++ \
        linux-headers > /dev/null \
    # Install redis
    && pecl bundle -d /usr/src/php/ext redis \
    && docker-php-ext-install -j2 redis \
    # Install intl
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j2 intl \
    # Install mcrypt
    && pecl bundle -d /usr/src/php/ext mcrypt \
    && docker-php-ext-configure mcrypt \
    # Install apcu
    && pecl bundle -d /usr/src/php/ext apcu \
    && docker-php-ext-install -j2 apcu \
    # Install Xdebug
    && pecl bundle -d /usr/src/php/ext xdebug-3.0.4 \
    && docker-php-ext-install xdebug \
    # Install others
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j2 bcmath gd iconv pdo pdo_mysql zip \
    && docker-php-ext-enable opcache \
    && rm /usr/src/php/ext/*.tgz \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --version=2.0.12 --filename=composer --install-dir=/usr/bin \
    && chmod +x /usr/bin/composer \
    && php -r "unlink('composer-setup.php');" \
    && composer --version
