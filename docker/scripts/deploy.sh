#!/usr/bin/env bash

# Include
# ------------------------------------------------------------------------
. $(dirname $(readlink -e "$0"))/functions.lib

while [ $# -gt 0 ]; do
    case "$1" in
    --stack-name=*) # docker stack name
        STACK_NAME="${1#*=}"
        ;;
    --docker-context=*) # remote docker context
        CONTEXT="${1#*=}"
        ;;
    --configuration=*) # image name suffix (dev/test/release etc)
        CONFIGURATION="${1#*=}"
        ;;
    esac
    shift
done

[ -z "${STACK_NAME}" ] && { echo "Please provide docker stack name"; exit 1; };

START_TIME=$(date +%s)
setEnvironment "${CONFIGURATION}" "DEPLOY" \
|| { echo "Error setEnvironment function!"; exit 1; };

echo "STACK_NAME=${STACK_NAME}"
echo "CONTEXT=${CONTEXT:="default"}"
echo "Start deploying services"

# deploy service images (not base)
docker --context ${CONTEXT} \
       stack deploy "${STACK_NAME}" \
       -c "${COMPOSE_FILE_DEPLOY}" \
       --prune \
       --with-registry-auth \
|| { echo "Deploy service images failed"; exit 1; };

FINISH_TIME=$(date +%s)
TIME=$(("${FINISH_TIME}" - "${START_TIME}"))
echo "Deploy finished at $TIME seconds."
