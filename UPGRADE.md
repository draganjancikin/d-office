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
* #135: On Order search, link to archived order is broken

# Upgrade to 6.0.3

Resolved issues and bugfixes:
* #133: Broken link from Project to Order

Enhancement:
* When perform advanced search repopulate search field

# Upgrade to 6.0.2

Resolved issues and bugfixes:
* #125: Material view and edit of non-existent id give an error
* #127: Accounting Document view and edit of non-existent id give an error
* #129: Material view on mobile and small display
* #130: CuttingSheet view and edit of non-existent id give an error

Enhancement:
* On Order add Material, list only Order Supplier Materials
* Remove unnecessary code an code refactoring

# Upgrade to 6.0.1

Enhancement:
* Resolved issue #104: Add confirmation step to delete Accounting Document

Bug fixes:
* Resolved issue #106: Adding file to project dont work
* Resolved issue #108: Search for archived project dont work
* Resolved issue #110: Order material print error
* Resolved issue #112: Create a new Project from AccountingDocument dont work
* Resolved issue #113: Print AccountingDocument error
* Resolved issue #116: Add same material in order error
* Resolved issue #118: Cities and Streets list need to be order by name
* Resolved issue #120: Export Proforma to Dispatch dont work
* Resolved issue #122: Cutting Sheets Calculations in no good

# Upgrade to 6.0.0

Bug fixes:

* Resolved issue #101: AccountingDocument Delete dont work
* Resolved issue #102: Create AccDoc from Project page

Updates:

* Update "Doctrine/ORM" (2.8.4 => 2.8.5)

# Upgrade to 6.0.0-beta1

* Inplement Doctrine 2.8.4 to application.

# Upgrade to 6.0.0-alpha1

* Add Doctrine 2.8.4 application.
