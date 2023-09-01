FROM unit:1.30.0-php8.2

RUN apt-get update && apt-get install -y libicu-dev libcurl4-openssl-dev libxml2-dev && docker-php-ext-install bcmath pdo pdo_mysql intl xml curl
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY . /www
RUN chown -R unit:unit /www && cd /www \
    && composer install \
    && php bin/console lexik:jwt:generate-keypair --overwrite \
    # && openssl genrsa -out config/jwt/private-test.pem -aes256 4096 \
    # && openssl rsa -pubout -in config/jwt/private-test.pem -out config/jwt/public-test.pem \
    && mv /www/nginx-unit/certificates/*.pem /docker-entrypoint.d/ \
    && mv /www/nginx-unit/config/*.json /docker-entrypoint.d/ 
# THE FOLLOWING IS FOR BUILDING THE REACT APP
RUN mkdir /usr/local/nvm
ENV NVM_DIR /usr/local/nvm
ENV NODE_VERSION 18.17.0
RUN curl https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.1/install.sh | bash \
    && . $NVM_DIR/nvm.sh \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default

ENV NODE_PATH $NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV PATH $NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH
RUN set -ex && node --version && cd /www/react-app && npm install && npm run build

