FROM php:8.4

COPY dev.sh /tmp/library-scripts/

USER root

ARG USERNAME=dev
ARG USER_UID=1000
ARG USER_GID=${USER_UID}

RUN groupadd --gid ${USER_GID} ${USERNAME} && \
    useradd -s /bin/bash --uid ${USER_UID} --gid ${USERNAME} -m ${USERNAME}

COPY --from=composer:2.9 /usr/bin/composer /usr/bin/composer

RUN apt-get update && export DEBIAN_FRONTEND=noninteractive && \
    bash /tmp/library-scripts/dev.sh "${USERNAME}"

RUN export DEBIAN_FRONTEND=noninteractive && apt-get update && \
    apt-get install -y libzip-dev && \
    docker-php-ext-install zip

RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN curl -sLo /usr/bin/phpdoc https://phpdoc.org/phpDocumentor.phar && chmod +x /usr/bin/phpdoc

RUN mkdir /app && chown ${USERNAME}: /app

WORKDIR /app

USER ${USERNAME}
