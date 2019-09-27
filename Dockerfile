FROM php:7.2

RUN apt-get update -y && apt-get install -y \
  build-essential openssl gnupg apt-transport-https git zip unzip libpng-dev libpq-dev

RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" >/etc/apt/sources.list.d/yarn.list

RUN curl -sL https://deb.nodesource.com/setup_10.x | bash -

RUN apt-get update -y && apt-get install -y nodejs yarn

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install pdo pdo_pgsql mbstring

RUN apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd

COPY docker/upload_max_filesize.ini $PHP_INI_DIR/conf.d/

RUN mkdir -p /app
WORKDIR /app

EXPOSE 8000
