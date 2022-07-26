#!/usr/bin/env bash

function showHelp() {
    echo "Perform remote docker pull for docker-compose file with configuration"
    echo "Usage: $(basename "${0}") <configuration>"
    echo
    echo "Parameters:"
    echo "  --docker-context=<context>                     remote docker context"
    echo "  --build-version=<version>                      docker image tag (latest/1.0 etc)"
    echo "  --image-configuration=<image-configuration>    image name suffix (dev/test/release etc)"
    echo "  --all-images                                   pull all images instead of built for docker registry"
    echo "  --help                                         show help message"
    exit
}

# if no parameters provided then show help message
if [[ "$#" -eq 0 ]]; then
    showHelp
fi

# remember start time to measure script executing time
START_TIME=$(date +%s)

# docker-compose file postfix (dev/test/prod etc)
CONFIGURATION=${1}

while [ $# -gt 0 ]; do
    case "$1" in
    # remote docker context
    --docker-context=*)
        CONTEXT="${1#*=}"
        ;;
    # image name suffix (dev/test/release etc)
    --image-configuration=*)
        IMAGE_CONFIGURATION="${1#*=}"
        ;;
    # pull all images instead of built for docker registry
    --all-images)
        PULL_ALL_IMAGES=true
        ;;
    esac
    shift
done

if [[ "${CONTEXT}" == "" ]]; then
    # if docker context parameter not set then use default value (local docker context)
    CONTEXT=default
fi

# get environment variables by configuration and other parameters
SCRIPTS_DIR=$(dirname "$(realpath "${0}")")
. "${SCRIPTS_DIR}"/export_compose_variables.sh "${CONFIGURATION}" --deploy

# https://stackoverflow.com/questions/5014632/how-can-i-parse-a-yaml-file-from-a-linux-shell-script
function parse_yaml {
    local prefix=$2
    local s='[[:space:]]*' w='[a-zA-Z0-9_]*' fs=$(echo @ | tr @ '\034')
    sed -ne "s|^\($s\):|\1|" \
        -e "s|^\($s\)\($w\)$s:$s[\"']\(.*\)[\"']$s\$|\1$fs\2$fs\3|p" \
        -e "s|^\($s\)\($w\)$s:$s\(.*\)$s\$|\1$fs\2$fs\3|p" $1 |
        awk -F$fs '{
      indent = length($1)/2;
      vname[indent] = $2;
      for (i in vname) {if (i > indent) {delete vname[i]}}
      if (length($3) > 0) {
         vn=""; for (i=0; i<indent; i++) {vn=(vn)(vname[i])("_")}
         printf("%s%s%s=\"%s\"\n", "'$prefix'",vn, $2, $3);
      }
   }'
}

function get_docker_images_yaml_lines_from_docker_compose_file {
    local COMPOSE_FILE=${1}

    # parse yaml lines from docker-compose file
    YAML_LINES=$(parse_yaml "${COMPOSE_FILE}")

    # Change IFS to new line
    IFS=$'\n'
    # split to array $YAML_LINES
    YAML_LINES=("${YAML_LINES}")

    local YAML_LINES_WITH_DOCKER_IMAGE_KEY_VALUE_pairs=()

    for YAML_LINE in "${YAML_LINES[@]}"; do
        # split YAML_LINE to array by '='
        IFS='=' read -r -a YAML_LINE_KEY_VALUE <<<"$YAML_LINE"
        YAML_KEY="${YAML_LINE_KEY_VALUE[0]}"

        # if YAML_KEY starts with "services_" and ends with "_image"
        if [[ $YAML_KEY == services_* && $YAML_KEY == *_image ]]; then
            YAML_LINES_WITH_DOCKER_IMAGE_KEY_VALUE_pairs+=("${YAML_LINE}")
        fi
    done

    # return
    echo "${YAML_LINES_WITH_DOCKER_IMAGE_KEY_VALUE_pairs[@]}"
}

# handle docker-compose files (root docker-compose.yml and docker-compose.%CONFIGURATION%.yml)
DOCKER_COMPOSE_FILES=("${COMPOSE_FILE_DEPLOY}")

# declare associative array for yaml key-value pairs
declare -A YAML_LINES_WITH_DOCKER_IMAGE_KEY_VALUE_PAIRS

# handle each docker-compose file
for DOCKER_COMPOSE_FILE in "${DOCKER_COMPOSE_FILES[@]}"; do
    # get all yaml lines from docker-compose file
    DOCKER_IMAGE_YAML_LINES_FROM_FILE=$(get_docker_images_yaml_lines_from_docker_compose_file "${DOCKER_COMPOSE_FILE}")

    # make an array
    DOCKER_IMAGE_YAML_LINES_FROM_FILE=("${DOCKER_IMAGE_YAML_LINES_FROM_FILE}")

    # handle each yaml line
    for YAML_LINE in "${DOCKER_IMAGE_YAML_LINES_FROM_FILE[@]}"; do
        # split YAML_LINE to array by '='
        IFS='=' read -r -a YAML_LINE_KEY_VALUE <<<"$YAML_LINE"
        YAML_KEY="${YAML_LINE_KEY_VALUE[0]}"
        YAML_VALUE=${YAML_LINE_KEY_VALUE[1]}

        # assign YAML KEY and YAML VALUE to associative array
        # it will overwrite exist image name from docker-compose.yml by docker-compose.%CONFIGURATION%.yml
        YAML_LINES_WITH_DOCKER_IMAGE_KEY_VALUE_PAIRS+=([${YAML_KEY}]=${YAML_VALUE})
    done
done

# next - transform yaml key-value pairs to docker images
DOCKER_IMAGES_TO_PULL=()

for YAML_VALUE in "${YAML_LINES_WITH_DOCKER_IMAGE_KEY_VALUE_PAIRS[@]}"; do
    # trim quotes
    YAML_VALUE="${YAML_VALUE%\"}"
    YAML_VALUE="${YAML_VALUE#\"}"

    # replace environment variables by their values
    DOCKER_IMAGE=$(eval "echo ${YAML_VALUE}")

    # if flag "pull all images" disabled, then handle only images starts with DOCKER_REGISTRY variable
    if [[ "$PULL_ALL_IMAGES" = true || $DOCKER_IMAGE == ${DOCKER_REGISTRY}* ]]; then
        DOCKER_IMAGES_TO_PULL+=("${DOCKER_IMAGE}")
    fi
done

# leave only unique values
DOCKER_IMAGES_TO_PULL_UNIQUE=("$(echo "${DOCKER_IMAGES_TO_PULL[@]}" | tr ' ' '\n' | sort -u | tr '\n' ' ')")

# then do "docker pull" with remote context for each docker image
for DOCKER_IMAGE in "${DOCKER_IMAGES_TO_PULL_UNIQUE[@]}"; do
    echo "pull image \"${DOCKER_IMAGE}\""
    docker --context ${CONTEXT} pull "${DOCKER_IMAGE}"
done

# measure script executing time
FINISH_TIME=$(date +%s)
TIME=$(("${FINISH_TIME}" - "${START_TIME}"))

echo "Remote pull finished at ${TIME} seconds."
