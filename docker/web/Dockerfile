# This docker image will be created from the official Docker PHP 5 image with Apache
FROM php:5-apache

# Copy PHP configuration file into the image
COPY ./config/php.ini /usr/local/etc/php/

# Copy Apache virtualhost config file into the image
# COPY ./config/000-default.conf /etc/apache2/sites-available/

# Download, compile and install ICU library required by intl PHP extension
RUN curl -sS -o /tmp/icu.tar.gz -L http://download.icu-project.org/files/icu4c/58.2/icu4c-58_2-src.tgz \
    && tar -zxf /tmp/icu.tar.gz -C /tmp \
    && cd /tmp/icu/source \
    && ./configure --prefix=/usr/local \
    && make \
    && make install

# Delete ICU source files from /tmp directory
RUN rm /tmp/icu.tar.gz && rm -r /tmp/icu

# Install PHP extensions required or recommended by Symfony (intl and opcache)
RUN docker-php-ext-configure intl --with-icu-dir=/usr/local \
    && docker-php-ext-install intl opcache

# Configure PHP GD for jpeg support
RUN apt-get update && apt-get install -y \
        # Install PHP extensions dependencies
        libjpeg62-turbo-dev \
        libpng-dev \
        zlib1g-dev \
    # Configure GD to point to the jpeglib directory
    && docker-php-ext-configure gd --with-jpeg-dir=/usr/include/ \
    # Install PHP's GD, Zip, and MySQLi extensions
    && docker-php-ext-install gd zip mysqli

# Enable mod_rewrite module on Apache to make .htaccess files work
RUN a2enmod rewrite

# Upgrade OS packages
RUN apt-get -y upgrade

# Install git
RUN apt-get -y install git

# Install tools required by Composer
RUN apt-get -y install git
RUN apt-get -y install zip

# Install Composer globally
WORKDIR /tmp
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN echo "Moving composer to /usr/local/bin/composer" && mv composer.phar /usr/local/bin/composer

# Enable Apache mod_expires.c
RUN a2enmod expires && service apache2 restart

# Change working directory to Apache's web document root
WORKDIR /var/www/html
