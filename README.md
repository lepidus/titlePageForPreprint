# Title Page Plugin
Generic plugin for OPS, creates a title page on PDF files submitted to Scielo servers.

## Installation dependencies 
* [poppler-utils](https://poppler.freedesktop.org/)

## How to use
1. Install the 'poppler-utils' dependency.
2. Add the plugin to OPS, by the administrator Control Panel. In case OPS raise the file size error, check out the variables on ´php.ini´: `upload_max_filesize` and `post_max_size` wich the values must be at least 17M.  
In order to avoid editing PHP configuration files, you can also install the plugin by unziping it to the directory `plugins/generic/`.
3. Activate the plugin on the Control Panel.

## Development dependencies
* [poppler-utils](https://poppler.freedesktop.org/)
* [php-imagick](https://www.php.net/manual/pt_BR/imagick.compareimages.php) - needed for unit tests.
* [phpunit](https://phpunit.de/) - version 8, to run unit tests.

## Installation for development
1. Install the development dependencies.
2. Clone the [repository](https://gitlab.lepidus.com.br/softwares-pkp/plugins_ojs/folhaDeRostoDoPDF)
3. Switch branch, if needed.
4. Run `composer install` inside the repository.
5. Modify the file: `/etc/ImageMagick-6/policy.xml` , to allow read/write permissions to PDF, changing this specific line: `<policy domain=“coder” rights="none" pattern=“PDF” />` 
to this:            `<policy domain=“coder” rights=“read|write” pattern=“PDF” />`
