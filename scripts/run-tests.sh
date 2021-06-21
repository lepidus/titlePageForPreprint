#!/usr/bin/env bash

set -e

testsCommand="phpunit --bootstrap classes/autoload.inc.php tests/"

echo "Running unit tests..."
eval "$testsCommand"