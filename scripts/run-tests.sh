#!/usr/bin/env bash

set -e

comandoParaTestes="phpunit --bootstrap fontes/autoload.inc.php testes/"

echo "Executando testes de unidade..."
eval "$comandoParaTestes"