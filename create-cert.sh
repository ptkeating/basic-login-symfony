#/bin/sh

openssl req -x509 -newkey rsa:4096 -keyout ./nginx-unit/certificates/key.pem -out ./nginx-unit/certificates/cert.pem -sha256 -days 3650 -nodes -subj "/C=GB/ST=England/L=London/O=TestCorp/OU=TestDept/CN=basic-login-symfony.local"
cat ./nginx-unit/certificates/key.pem ./nginx-unit/certificates/cert.pem > ./nginx-unit/certificates/bundle.pem
rm ./nginx-unit/certificates/key.pem ./nginx-unit/certificates/cert.pem
