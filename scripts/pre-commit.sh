#!/usr/bin/env bash

echo "Calling pre-commit hook..."
./scripts/run-tests.sh

if [ $? -ne 0 ]; then
    echo "-------All tests must pass before the commit!-------"
    exit 1
fi