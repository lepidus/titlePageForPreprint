# Title Page Plugin

This plugin creates a title page on PDF files submitted to preprint servers. The title page is a page added to the beginning of the PDF file, containing a series of information about the preprint when it is posted. After the preprint is posted, the title page is also updated if the preprint relations are changed.

The common information obtained for PDF title page are:

- Relation status
- Publication DOI (if it was published in a journal)
- Preprint title
- Preprint authors
- Preprint DOI in current server
- Submission preparation checklist
- Submitted date 
- Posted date

## Compatibility

The latest release of this plugin is compatible with the following PKP applications:

* OPS 3.3.0

## Plugin Download

To download the plugin, go to the [Releases page](https://github.com/lepidus/titlePageForPreprint/releases) and download the tar.gz package of the latest release compatible with your website.

## Development dependencies
* [php-imagick](https://www.php.net/manual/pt_BR/imagick.compareimages.php) - needed for unit tests.
* [phpunit](https://phpunit.de/) - version 8, to run unit tests.

## Installation

1. Enter the administration area of ​​your OPS website through the __Dashboard__. In case OPS raise the file size error, check out the variables on ´php.ini´: `upload_max_filesize` and `post_max_size` wich the values must be at least 17M.
2. Navigate to `Settings`>` Website`> `Plugins`> `Upload a new plugin`.
3. Under __Upload file__ select the file __titlePageForPreprint.tar.gz__.
4. Click __Save__ and the plugin will be installed on your website.
5. This plugin requires the CPDF binary at `tools/` being executable at your server. The plugin won't execute its features until this binary is marked as executable.
6. This plugin also requires a logo defined on `Website Settings` > `Apearance` > `Logo`, so you need to upload an image. Images with any kind of transparency (alpha channel) are not supported and **should not** be used.

## Installation for development
1. Install the development dependencies.
2. Clone the [repository](https://github.com/lepidus/titlePageForPreprint)
3. Switch branch, if needed.
4. Run `composer install` inside the repository.
5. Modify the file: `/etc/ImageMagick-6/policy.xml` , to allow read/write permissions to PDF, changing this specific line: `<policy domain=“coder” rights="none" pattern=“PDF” />` 
to this:            `<policy domain=“coder” rights=“read|write” pattern=“PDF” />`
6. Run `npm install` inside the repository to install module for the automated acceptance test.

# License

Since this plugin uses the CPDF library, make sure to check [its license](https://github.com/coherentgraphics/cpdf-binaries/blob/master/LICENSE) in order to know if your organization can use it.

__This plugin is licensed under the GNU General Public License v3.0__

__Copyright (c) 2020-2021 Lepidus Tecnologia__

__Copyright (c) 2020-2021 SciELO__
