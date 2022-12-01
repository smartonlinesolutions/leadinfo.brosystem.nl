FROM php:7.4-apache

ENV APACHE_DOCUMENT_ROOT /var/www/html/web

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Install dependencies
RUN apt-get update && apt-get install --no-install-recommends -y \
        less \
        libfreetype6-dev \
        libicu-dev \
        libjpeg-dev \
        libmcrypt-dev \
        libxml2-dev \
        libzip-dev \
        mariadb-client \
        msmtp \
        sudo \
        unzip \
        zlib1g-dev

# Install and configure PHP extensions
RUN docker-php-source extract

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl

RUN docker-php-ext-install -j$(nproc) \
        bcmath \
        exif \
        gd \
        intl \
        pdo_mysql \
        mysqli \
        opcache \
        soap \
        zip

RUN pecl install xdebug-2.9.8 \
    && docker-php-ext-enable xdebug

RUN { \
        echo 'post_max_size=500M'; \
        echo 'upload_max_filesize=500M'; \
        echo 'log_errors=1'; \
        echo 'error_reporting=E_ALL'; \
    } > /usr/local/etc/php/php.ini

RUN { \
        echo 'xdebug.idekey=PHPSTORM'; \
        echo 'xdebug.remote_autostart=0'; \
        echo 'xdebug.remote_connect_back=0'; \
        echo 'xdebug.default_enable=0'; \
        echo 'xdebug.remote_enable=1'; \
        echo 'xdebug.remote_host=docker.for.mac.localhost'; \
        echo 'xdebug.profiler_enable=0'; \
        echo 'xdebug.profiler_enable_trigger=1'; \
    } > /usr/local/etc/php/conf.d/xdebug.ini

RUN { \
        echo 'opcache.memory_consumption=256'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=10000'; \
        echo 'opcache.revalidate_freq=2'; \
        echo 'opcache.fast_shutdown=1'; \
        echo 'opcache.enable_cli=1'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

RUN docker-php-source delete

# Enable Apache modules
RUN a2enmod rewrite expires

# Add WP-CLI
RUN curl -o /bin/wp-cli.phar https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
COPY wp-su.sh /bin/wp
RUN chmod +x /bin/wp-cli.phar /bin/wp

# Add XDebug enabling/disabling scripts
COPY xdebug-enable.sh /bin/xdebug-enable
COPY xdebug-disable.sh /bin/xdebug-disable
RUN chmod +x /bin/xdebug-enable /bin/xdebug-disable

# SSMTP settings
COPY msmtp.conf /etc/ssmtp/msmtp.conf

# PHP mail settings
RUN echo 'sendmail_path = "/usr/bin/msmtp --file=/etc/ssmtp/msmtp.conf -t -i"' > /usr/local/etc/php/conf.d/mail.ini
