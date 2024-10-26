# Upgrade to 6.6.3
Resolved issues and bugfixes:
* #215 - Table head for material suppliers

# Upgrade to 6.6.2
* #139 - Table head for material suppliers
* #162 - Create a form for create and edit Article group
* #203 - Warning: Undefined variable $client_street_name in .../form_advanced_search.php on line 102

# Upgrade to 6.6.1
* #186 - Advanced search on Client section issue
* #190 - Transaction note don't display in transactions listing

# Upgrade to 6.6.0
* #198 - Add edit feature to Material on Order page

# Upgrade to 6.5.2

Resolved issues and bugfixes:
* #194 - Error on Projects page'
* #197 - Update Deprecated Doctrine\ORM\Tools\Setup

Enhancement:
* On client preview page remove disabled attribute from client contacts
* On new client page set default country to "Srbija"
* Update "doctrine/orm" (2.12.4 => 2.13.5)

# Upgrade to 6.5.1

Resolved issues and bugfixes:
* #178 - php 7.3 compatibility issue
* #179 - Issue on search on Client page
* #180 - Make a phone number call-able on client page
* #183 - Error on page Dokuments-Transactions
* #184 - Error on page: Order view
* #185 - Update structure of tools menu on Clients page

# Upgrade to 6.5.0

Enhancement:
* Move app/includes to templates/includes and code refactoring.
* Replaced "Doctrine\ORM\Tools\Setup" class with "Doctrine\ORM\ORMSetup". Updated bootstrap.php.
* Installed "symfony/cache" (5.4.15)

# Upgrade to 6.4.2

Resolved issues and bugfixes:
* #169 - Error on page create a new Project
* #170 - Error on Project page
* #171 - Error on page printProjectTask
* #173 - Error on page create a new Cutting
* #174 - Error on page view CuttingSheet

Enhancement:
* Getting Client data move from template to ClientRepository

# Upgrade to 6.4.1

Resolved issues and bugfixes:
* #159 - Issue with print on Accounting Document page
* #160 - Create an article issue
* #161 - Issue on edit article page
* #163 - Create a new "Narudzbenica"
* #164 - Error on page "edit Material Supplier"
* #165 - Error on page add material to order
* #166 - Error on page printOrder
* #167 - Error on page "Add article to Accounting Document

# Upgrade to 6.4.0

Enhancement:
* Change name of application, from "RolOffice" to "d-Office"
* Create Admin section for Company data and remove company data from code

# Upgrade to 6.3.1

Resolved issues and bugfixes:
* #154 - PHP issue

# Upgrade to 6.3.0

Enhancement:
* Create client form:
  * Fix typo
  * Remove value from default select option

Resolved issues and bugfixes:
* #146 - Client view page bug
* #147 - Client search don't work
* #148 - Create Accounting document issue
* #149 - View Accounting Document issue
* #150 - Issue on Accounting Document edit page
* #151 - Issue on Accounting Document print page v2

Updates:
* "doctrine/orm" (2.11.3 => 2.13.4)
* "doctrine/annotations" (1.13.2 => 1.14.1)

# Upgrade to 6.2.1

Updates:
* "doctrine/orm" (2.9.5 => 2.11.3)
* "doctrine/cache" ( => 1.12.1)
* "tecnickcom/tcpdf" (6.4.2 => 6.4.4)
* "symfony/http-foundation" (5.1.4 => 5.4.8)

# Upgrade to 6.2.0

Updates:
* Installed "symfony/http-foundation" (5.4.1)

# Upgrade to 6.1.0

Updates:
* Update "Doctrine/ORM" (2.8.5 => 2.9.5)
* Update "tecnickcom/tcpdf" (6.3.5 => 6.4.2)

Enhancement:
* Add Material modified date to Material on view and edit page
* Add Material modified date to Material Supplier on view and edit
* Add TCPDF trough composer
* Move printProjectTaskWithNotes.php, printProjectTask.php and printProjectTask.php to Project
* Move printAccountingDocuments and printDailyCachReport to pidb
* Move printCutting to cutting
* Move printOrder to order
* Remove TCPDF from code base

New features:
* Add edit/delete form for AccountingDocument Transaction (payment)

# Upgrade to 6.0.4

Resolved issues and bugfixes:
* #135 - On Order search, link to archived order is broken

# Upgrade to 6.0.3

Resolved issues and bugfixes:
* #133 - Broken link from Project to Order

Enhancement:
* When perform advanced search repopulate search field

# Upgrade to 6.0.2

Resolved issues and bugfixes:
* #125 - Material view and edit of non-existent id give an error
* #127 - Accounting Document view and edit of non-existent id give an error
* #129 - Material view on mobile and small display
* #130 - CuttingSheet view and edit of non-existent id give an error

Enhancement:
* On Order add Material, list only Order Supplier Materials
* Remove unnecessary code an code refactoring

# Upgrade to 6.0.1

Enhancement:
* Resolved issue #104: Add confirmation step to delete Accounting Document

Bug fixes:
* #106 - Adding file to project dont work
* #108 - Search for archived project dont work
* #110 - Order material print error
* #112 - Create a new Project from AccountingDocument dont work
* #113 - Print AccountingDocument error
* #116 - Add same material in order error
* #118 - Cities and Streets list need to be order by name
* #120 - Export Proforma to Dispatch dont work
* #122 - Cutting Sheets Calculations in no good

# Upgrade to 6.0.0

Bug fixes:

* #101 - AccountingDocument Delete dont work
* #102 - Create AccDoc from Project page

Updates:

* Update "Doctrine/ORM" (2.8.4 => 2.8.5)

# Upgrade to 6.0.0-beta1

* Inplement Doctrine 2.8.4 to application.

# Upgrade to 6.0.0-alpha1

* Add Doctrine 2.8.4 application.
