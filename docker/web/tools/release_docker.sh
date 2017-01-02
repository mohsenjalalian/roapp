#!/bin/bash

set -e

if [ -z "$DOCKER_REGISTRY" ]; then
    echo "Missing DOCKER_REGISTRY env var pointing to Docker registry"
    exit 1
fi

if [ -z "$TRAVIS_TAG" ]; then
    echo "Missing TRAVIS_TAG env var identifying version"
    exit 1
fi

pip install --user awscli          # aws cli needed to run `aws ecr get-login`
export PATH=$PATH:$HOME/.local/bin # Get aws cli on path

################################################
### Cleaning up files
################################################
rm -rf .git/ \
    tests \
    travis

################################
# Building Image and artifact
################################

# Build the docker image
docker build -t ${REPOSITORY_NAME} .

# Use aws-cli to generate a "docker login" command and run it
# us-east-1 is the currently supported ECR region for cloudformation, we'll be paying for data transfer
`aws ecr get-login --region us-east-1`

# Tag the locally built image to remote
docker tag ${REPOSITORY_NAME}:latest ${DOCKER_REGISTRY}/${REPOSITORY_NAME}:${TRAVIS_TAG}

# Push docker image to remote
docker push ${DOCKER_REGISTRY}/${REPOSITORY_NAME}:${TRAVIS_TAG}
