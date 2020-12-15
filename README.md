# RolOffice v5.0.4

Web Application 

New for version 5.1.0:
- add Composer and composer autoload
- add transaction type to transaction list by pidb
- on database table transaction rename columns transaction_type_id to type_id and update code
- add transaction: add custom date, add created_at, created_at_user_id
- add new metthod getDate() to DatabaseContoller
- on payment page allow decimal separator ","
- updated double comparision
- add new index: "type_name_abb" to method getPidb()

Enhancement:
- CSS tuning for Order and Pidb forms
- Replace "eur" with € on Pidb fomms
