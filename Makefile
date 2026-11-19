# Minimum version of PHP supported by this project
PHP_VERSION ?= 8.3
PHP_EXTENSIONS ?= bcmath gd intl mbstring json xdebug
DOCKER_IMAGE_NAME = laminas/form

# Includes _everything_ supplied by the internal tooling lib
include vendor/laminas/internal-tooling/Makefile
