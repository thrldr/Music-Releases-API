#!/bin/sh

source .env.local
composer symfony composer dump-env
docker-compose up -d
symfony serve -d