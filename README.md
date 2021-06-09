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

## Installation dependencies 
* [poppler-utils](https://poppler.freedesktop.org/)

## Development dependencies
* [poppler-utils](https://poppler.freedesktop.org/)
* [php-imagick](https://www.php.net/manual/pt_BR/imagick.compareimages.php) - needed for unit tests.
* [phpunit](https://phpunit.de/) - version 8, to run unit tests.

## Installation

1. Install the 'poppler-utils' dependency.
2. Enter the administration area of ​​your OPS website through the __Dashboard__. In case OPS raise the file size error, check out the variables on ´php.ini´: `upload_max_filesize` and `post_max_size` wich the values must be at least 17M.
3. Navigate to `Settings`>` Website`> `Plugins`> `Upload a new plugin`.
4. Under __Upload file__ select the file __titlePageForPreprint.tar.gz__.
5. Click __Save__ and the plugin will be installed on your website.
6. The plugin requires a logo defined on Website Settings -> Apearance -> Logo, so you need to upload an image. Images with any kind of transparency (alpha channel) are not supported and **should not** be used.

## Installation for development
1. Install the development dependencies.
2. Clone the [repository](https://github.com/lepidus/titlePageForPreprint)
3. Switch branch, if needed.
4. Run `composer install` inside the repository.
5. Modify the file: `/etc/ImageMagick-6/policy.xml` , to allow read/write permissions to PDF, changing this specific line: `<policy domain=“coder” rights="none" pattern=“PDF” />` 
to this:            `<policy domain=“coder” rights=“read|write” pattern=“PDF” />`

# License
__This plugin is licensed under the GNU General Public License v3.0__

__Copyright (c) 2020-2021 Lepidus Tecnologia__

__Copyright (c) 2020-2021 SciELO__
