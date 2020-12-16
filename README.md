# RolOffice v5.1.2

Web Application 

## New for version 5.1.0
- add Composer and composer autoload
- add transaction type to transaction list by pidb
- on database table transaction rename columns transaction_type_id to type_id and update code
- add transaction: add custom date, add created_at, created_at_user_id
- add new metthod getDate() to DatabaseContoller
- on payment page allow decimal separator ","
- updated double comparision
- add new index: "type_name_abb" to method getPidb()
- add cash register page

Enhancement:
- little CSS tuning for Order and Pidb forms
- Replace "eur" with € 

## New for version 5.1.1
- Replace variable $version with const VERSION
- change file name for dump database

## New for version 5.1.2
- Move const ENV to dbConfig
