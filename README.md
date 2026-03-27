A web application for office and project management, built with PHP 8.2 and modern libraries.

## Libraries & Requirements

- **PHP:** 8.2 or higher
- **Database ORM:**
  - doctrine/orm ^3.3
  - doctrine/doctrine-bundle ^2.15
- **Frontend:** Bootstrap 5.3.8
- **PDF Generation:** tecnickcom/tcpdf ^6.3
- **Framework Components:**
  - symfony/asset ^7.4
  - symfony/console ^7.4
  - symfony/dotenv ^7.4
  - symfony/flex ^2.10
  - symfony/form ^7.4
  - symfony/framework-bundle ^7.4
  - symfony/process ^7.4
  - symfony/runtime ^7.4
  - symfony/twig-bundle ^7.4
  - symfony/yaml ^7.4
  - symfony/validator ^7.4
- **Development Dependencies:**
  - symfony/debug-bundle ^7.4
  - symfony/maker-bundle ^1.64
  - symfony/stopwatch ^7.4
  - symfony/web-profiler-bundle ^7.4

## Installation

###

1. Install PHP 8.2 and Composer.
2. Run `composer install` to install dependencies.
3. Configure your database and environment settings in `config/`.
4. Set up your web server to serve the `public/` directory.

### With Docksal

1. Go to projects folder
2. Clone project from GitHub: 
'''
git clone git@github.com:draganjancikin/d-office.git [project-name]
'''
3. Go to [project-name] folder
4. Start Docksal
'''
fin up
'''

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