#!/usr/bin/env bash

echo "Chamando o hook pre-commit"
./scripts/run-tests.sh

if [ $? -ne 0 ]; then
    echo "-------Todos os testes devem passar antes do commit!-------"
    exit 1
fi