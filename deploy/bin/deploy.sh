#!/usr/bin/env bash

set -ex

: "${PROJECT_PARENT_DIR?Missing environment variable: PROJECT_PARENT_DIR}"
: "${DEPLOY_ENVIRONMENT?Missing environment variable: DEPLOY_ENVIRONMENT}"
: "${PRIVATE_KEY?Missing environment variable: PRIVATE_KEY}"

HOSTS=""

if [ "$DEPLOY_ENVIRONMENT" = "staging" ]; then
    HOSTS="../hosts_staging"
else
    HOSTS="../hosts_production"
fi

ansible-playbook ../deploy.yml \
  --i "$HOSTS" \
  --extra-vars "PROJECT_PARENT_DIR=$PROJECT_PARENT_DIR" \
  --extra-vars "DEPLOY_ENVIRONMENT=$DEPLOY_ENVIRONMENT" \
  --private-key "$PRIVATE_KEY"
