# FolhaDeRostoPlugin
Plugin do OPS para criação de folha de rosto nos arquivos submetidos no servidor Scielo.

## Dependências de Instalação 
* [poppler-utils](https://poppler.freedesktop.org/)

## Como utilizar 
1. Instale a dependência
2. Adicione o plugin pelo painel de controle do Administrador do servidor (no arquivo php.ini verifique as variáveis: upload_max_filesize e post_max_size mude para igual ou maior que 37M) ou descompacte o arquivo no diretório `plugins/generic/`
3. Ative o plugin no painel de controle

## Dependências para Desenvolvimento
* [poppler-utils](https://poppler.freedesktop.org/)
* [php-imagick](https://www.php.net/manual/pt_BR/imagick.compareimages.php)
* [phpunit](https://phpunit.de/) - em função dos testes

## Instalação para Desenvolvimento
1. Clone o [repositório](https://gitlab.lepidus.com.br/softwares-pkp/plugins_ojs/folhaDeRostoDoPDF);
2. Troque de branch (se necessário);
3. Execute composer install;
4. Modifique o arquivo: /etc/ImageMagick-6/policy.xml , para permitir a escrita/leitura de arquivos PDF, mudando a linha que especifica permissões relacionadas a arquivos PDF para:
<policy domain=“coder” rights=“read|write” pattern=“PDF” /> 
