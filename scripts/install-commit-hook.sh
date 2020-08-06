 #!/usr/bin/env bash

GIT_DIR=$(git rev-parse --git-dir)

echo "Instalando hook de pre-commit..."

chmod +x run-tests.sh pre-commit.sh
ln -s ../../scripts/pre-commit.sh $GIT_DIR/hooks/pre-commit

echo "Feito!"
