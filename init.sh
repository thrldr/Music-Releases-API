#!/bin/sh

symfony composer dump-env
docker-compose up -d
symfony serve -d