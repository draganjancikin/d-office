# RolOffice v5.3.5 - 6.0.3

Web Application

## New in version 5.1.0

- Add Composer and composer autoload
- Add transaction type to transaction list by pidb
- On database table transaction rename columns transaction_type_id to type_id and update code
- Add transaction: add custom date, add created_at, created_at_user_id
- Add new metthod getDate() to DatabaseContoller
- On payment page allow decimal separator ","
- Updated double comparision
- Add new index: "type_name_abb" to method getPidb()
- Add cash register page

Enhancement:

- Little CSS tuning for Order and Pidb forms
- Replace "eur" with €

## New in version 5.1.1

- Replace variable $version with const VERSION
- Change file name for dump database

## New in version 5.1.2

- Move const ENV to dbConfig

## New in version 5.1.3

- Resolved issue #56: Fatal error : Uncaught Error ...
- Delete unnecessary files

## New in version 5.1.4

- Resolved issue #57: Fatal error : Uncaught Error ...

## New in version 5.1.5

Issue resolved:

- #60 Add ROLO-TIM NS to printOrder

Enhancement:

- Little CSS tuning for Pidb view an edit forms
- Delete unnecessary files

## New in version 5.1.6

- Enhancement for require autoload in front index.php
- installationRecord update

## New in version 5.1.7

- .gitignore update
- Remove permissions for leftSidebarMenu, and change indentation to 2 spaces
- Change indentation for app/index.php to 2 spaces
- Change indentation for templates/pidb/includes/listLastTransaction.php to 2 spaces

## New in version 5.2.0

- Add new feature: Daily cash report

## New in version 5.2.1

- Code cleanup

## New in version 5.2.2

- Change type id in PidbController

## New in version 5.2.3

- Add new transaction type in Pidb

## New in version 5.2.4

- Add date param in cashRegister path

## New in version 5.2.5

- ORDER in last Transaction change to DESC
- Daily cash list update

## New in version 5.2.6

- Remove "Plaćanja" from leftSidebarMenu
- Move Controller/DatabaseController to Core/Database

## New in version 5.3.0

## New in version 5.3.1

- Remove folder vendor from .gitignore

## New in version 5.3.2

- Change client.vps_id to client.type_id

## New in version 5.3.3

- Add Doctrine package to application

## New in version 5.3.4

- Resolved issue #81 

## New in version 5.3.5

- Resolved issue #83

## New in version 5.3.5 - 6.0.0-alpha-1

- Added Doctrine ORM 2.8.1 layer to application. and implemented to Client part of App

## New in version 6.0.2

- Code cleanup
- Change user level to user role
- Remove bootstrap.php from git

## New in version 6.0.3

- Resolved issue #88: print Order error
