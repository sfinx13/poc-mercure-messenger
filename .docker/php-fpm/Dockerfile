FROM php:8.0-fpm
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        curl \
        wget \
        zlib1g-dev \
        libxml2-dev \
        libzip-dev \
        unzip \
        sqlite3 \
        && curl -fsSL https://deb.nodesource.com/setup_lts.x | bash \
        && apt-get install -y  nodejs \
         # Install intl
        && docker-php-ext-configure intl \
        && docker-php-ext-install -j2 intl \
        # Install apcu
        && pecl bundle -d /usr/src/php/ext apcu \
        && docker-php-ext-install -j2 apcu \
        # Install Xdebug
        && pecl bundle -d /usr/src/php/ext xdebug-3.0.4 \
        && docker-php-ext-install xdebug \
        # Install others
        && docker-php-ext-install -j2 bcmath iconv pdo pdo_mysql pdo_sqlite zip \
        && docker-php-ext-enable opcache \
        && rm /usr/src/php/ext/*.tgz \
        && curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

WORKDIR /application/