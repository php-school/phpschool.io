FROM php:8.0-fpm

RUN apt-get -qq update && apt-get install -qqy git zlib1g-dev libzip-dev \
	&& rm -rf /var/lib/apt/lists/* \
	&& docker-php-ext-install pdo pdo_mysql zip

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#RUN pecl install xdebug && docker-php-ext-enable xdebug
#ADD .docker/etc/php-xdebug.ini /usr/local/etc/php/conf.d/php-xdebug.ini

COPY . /var/www/html
WORKDIR /var/www/html

RUN cd /var/www/html && composer install -q --no-dev -o

CMD ["php-fpm"]