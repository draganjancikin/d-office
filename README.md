# d-Office 8.2.3

A web application for office and project management, built with PHP 8.2 and modern libraries.

## Libraries & Requirements

- **PHP:** 8.2 or higher
- **Database ORM:** Doctrine ORM ^3.3
- **Frontend:** Bootstrap 5.2.3
- **PDF Generation:** tecnickcom/tcpdf ^6.3
- **Templating:** twig/twig ^3.0
- **Framework Components:**
  - symfony/http-foundation ^6.0
  - symfony/cache ^6.0

## Installation

1. Install PHP 8.2 and Composer.
2. Run `composer install` to install dependencies.
3. Configure your database and environment settings in `config/`.
4. Set up your web server to serve the `public/` directory.

## Project Task Type Classes

The following CSS classes are used for project task types:

- info
- warning
- secondary
- success
- isporuka
- yellow
- danger
- popravka

> Note: Some class names were corrected for spelling (e.g., `warnning` → `warning`, `suvvedd` → `success`).

## License

See LICENCE file for details.