FROM php:8.1-cli

WORKDIR /app

RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    libxml2-dev \
    pkg-config \
    libssl-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j"$(nproc)" dom zip gd \
  && git clone --depth 1 --branch 1.20.1 --recurse-submodules https://github.com/mongodb/mongo-php-driver.git /tmp/mongo-php-driver \
  && cd /tmp/mongo-php-driver \
  && git submodule update --init --recursive \
  && phpize \
  && ./configure \
  && make -j"$(nproc)" \
  && make install \
  && rm -rf /tmp/mongo-php-driver \
  && docker-php-ext-enable mongodb \
  && pecl install xdebug \
  && docker-php-ext-enable xdebug \
  && rm -rf /var/lib/apt/lists/*

COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

CMD ["php", "-v"]

