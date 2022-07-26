#!/usr/bin/env bash

# Include
# ------------------------------------------------------------------------
. $(dirname $(readlink -e "$0"))/functions.lib

while [ $# -gt 0 ]; do
    case "$1" in
    --no-cache) # build "no-cache" flag (empty or any value)
        NO_CACHE="--no-cache"
        ;;
    --configuration=*) # image name suffix (dev/test/release etc)
        CONFIGURATION="${1#*=}"
        ;;
    esac
    shift
done

START_TIME=$(date +%s)
setEnvironment "${CONFIGURATION}" "BUILD" \
|| { echo "Error setEnvironment function!"; exit 1; };

echo ""
echo "Start building ${CONFIGURATION} image"
echo "NO_CACHE=${NO_CACHE}"

# build images by configuration
docker-compose -f "${COMPOSE_FILE_BUILD}" build \
                                          ${NO_CACHE} \
                                          --progress=plain \
                                          --parallel \
|| { echo "Build ${CONFIGURATION} images failed"; exit 1; };

FINISH_TIME=$(date +%s)
TIME=$(("${FINISH_TIME}" - "${START_TIME}"))
echo "Build finished at $TIME seconds."
