#!/usr/bin/env bash

set -e

testsCommand="phpunit --bootstrap fonts/autoload.inc.php tests/"

echo "Running unit tests..."
eval "$testsCommand"