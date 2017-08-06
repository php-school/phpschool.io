FROM php:7.1-fpm

RUN apt-get -qq update && apt-get install -qqy git zlib1g-dev \
	&& rm -rf /var/lib/apt/lists/* \
	&& docker-php-ext-install pdo pdo_mysql zip

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Xdebug
RUN pecl install -o -f xdebug-2.5.5

ADD .docker/etc/php-xdebug.ini /usr/local/etc/php/conf.d/php-xdebug.ini
RUN docker-php-ext-enable xdebug

COPY . /var/www/html
WORKDIR /var/www/html

RUN cd /var/www/html && composer install -q --no-dev -o
RUN php bin/app generate-docs

CMD ["php-fpm"]