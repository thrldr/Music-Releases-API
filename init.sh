#!/bin/sh

source ./.env.local
symfony composer dump-env
docker-compose up -d
symfony serve -d