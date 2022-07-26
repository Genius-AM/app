#!/usr/bin/env bash

# Include
# ------------------------------------------------------------------------
. $(dirname $(readlink -e "$0"))/functions.lib

while [ $# -gt 0 ]; do
    case "$1" in
    --configuration=*) # image name suffix (dev/test/release etc)
        CONFIGURATION="${1#*=}"
        ;;
    esac
    shift
done

START_TIME=$(date +%s)

setEnvironment "${CONFIGURATION}" "BUILD" \
|| { echo "Error setEnvironment function!"; exit 1; };

echo "Start pushing images"

# push service images (not base)
docker-compose -f "${COMPOSE_FILE_BUILD}" push \
|| { echo "Push ${CONFIGURATION} images failed"; exit 1; };

FINISH_TIME=$(date +%s)
TIME=$(("${FINISH_TIME}" - "${START_TIME}"))

echo "Push finished at ${TIME} seconds."
