#!/bin/sh

composer symfony composer dump-env
docker-compose up -d
symfony serve -d