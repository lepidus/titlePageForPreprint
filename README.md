# FolhaDeRostoPlugin
Plugin do OPS para criação de folha de rosto nos arquivos PDF submetidos no servidor Scielo.

## Dependências de Instalação 
* [poppler-utils](https://poppler.freedesktop.org/)

## Como utilizar 
1. Instale a dependência poppler-utils.
2. Adicione o plugin pelo painel de controle do Administrador do servidor. Caso o OPS informe erro com o tamanho do arquivo, confira a configuração do php.ini com relação as variáveis: upload_max_filesize e post_max_size, cujo valor deve ser de pelo menos 17M). Ou descompacte o plugin no diretório `plugins/generic/`, neste caso não é necessário ajuste em arquivos de configuração do PHP.
3. Ative o plugin no painel de controle.

## Dependências para Desenvolvimento
* [poppler-utils](https://poppler.freedesktop.org/)
* [php-imagick](https://www.php.net/manual/pt_BR/imagick.compareimages.php) - para executar os testes de unidade
* [phpunit](https://phpunit.de/) - versão 8, para executar os testes de unidade

## Instalação para Desenvolvimento
1. Realize a instalação das dependências para desenvolvimento.
2. Clone o [repositório](https://gitlab.lepidus.com.br/softwares-pkp/plugins_ojs/folhaDeRostoDoPDF)
3. Troque de branch (se necessário).
4. Execute `composer install` dentro do repositório.
5. Modifique o arquivo: /etc/ImageMagick-6/policy.xml , para permitir a escrita/leitura de arquivos PDF, mudando a linha que especifica permissões relacionadas a arquivos PDF:
De: <policy domain=“coder” rights="none" pattern=“PDF” />
Para:<policy domain=“coder” rights=“read|write” pattern=“PDF” /> 
