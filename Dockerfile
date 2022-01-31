FROM php:7.3-apache

# RUN apt update && apt install -y php-mysql php-xml php-mbstring
RUN apt update && apt install -y unzip
# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mysqli

# enable rewrite mod
RUN a2enmod rewrite

# install composer
RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
RUN php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer

# copy files
COPY api /var/www/html/api

# set workdir 
WORKDIR /var/www/html/api

# install php deps
RUN composer install
