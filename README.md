# epartool-downloader

This script makes setting up the ePartool on a remote webhosting much easier: it
* checks your server environment,
* gives you some hints for improvement,
* downloads and extracts the latest version of the ePartool within seconds, and
* launches the installation/configuration wizard.

## How to use

1. Copy the epartool-downloader.php to your webhosting, straight to the directory in which the ePartool is supposed to reside.
2. Start the helper by the use of your browser, e.g. www.example.com/epartool-downloader.php
3. The ePartool downloader welcomes you with an information screen and guide you through the rest of the process.

## Known limitations

* The script tries to recognise the user language, but offers only German or English (for any non-German locales).
* Currently the script does only test PHP version, available memory, max execution time and encrypted connection, but no dependencies or file permissions.
* The ePartool downloader script does not yet delete itself after its task is done, because the installation/configuration wizard is launched by a .htaccess redirect before the file unlink can be triggered. We are working on it ;-).

## Know more
The ePartool is an online consultation application written in PHP. It has originally been launched as substitute for an online questionnaire, because modules like voting and a visualisation of outcomes of the consultations were needed.
Further documentation on the ePartool itself is mostly available at https://tooldoku.dbjr.de (in German only at the moment).
