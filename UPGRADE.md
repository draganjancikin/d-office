# Upgrade to 8.2.1
Bug:
* #336 - Update tools menu links for creating a new project

# Upgrade to 8.2.0
Enhancement:
* #311 - Move forms into separate folders
* #328 - Oct system update:
  - "doctrine/orm" object relational mapper (3.3.2 => 3.5.2)
  - "symfony/http-foundation" commponent (6.1.12 => 6.4.26)
  - "symfony/cache" commponent (6.4.20 => 6.4.26)
  - "twig/twig" (3.20.0 => 3.21.1)
  - "tecnickcom/tcpdf" (6.9.4 => 6.10.0)
  - "bootstrap" (5.1.3 => 5.2.3)

# Upgrade to 8.1.4
Bug:
* #312 - Add "nl2br" into Accounting documents note
* #315 - Remove a thousand dot from property value before saving

Enhancement:
* #318 - Limit client contacts to 3 on project view page
* #321 - Add "article_middle_height" to Cutting pdf

# Upgrade to 8.1.3
Enhancement:
* #236 - Create employee view/edit page

# Upgrade to 8.1.2
Bug:
* #306 - Wrong listing direction of accounting documents on project page

# Upgrade to 8.1.1
Bug:
* #302 - Resolve picket length on Accounting document

# Upgrade to 8.1.0
Enhancement:
* #297 - Install "twig" package (3.20.0)

# Upgrade to 8.0.2
Enhancement:
* #296
 - Upgrade App configuration files
 - Update print config and files  

# Upgrade to 8.0.1
Enhancement:
* #292 - Delete config/bootstrap.php and remove from code

# Upgrade to 8.0.0
Upgrade:
* #284 - Change Entity metadata from Annotation to Attribute
  - Upgrade "doctrine/orm" php package(2.20.0 => 3.3.2)
  - Replace annotation with attributes on entities:
    - Client, ClientType, Country, City, Street, Contact, ContactType, User, UserRole.
    - AccountingDocument, AccountingDocumentArticle, AccountingDocumentArticleProperty, AccountingDocumentType, 
      Article, ArticleGroup, ArticleProperty, Payment, PaymentType, Preferences, Property, Unit, 
      AccountingDocumentArticleRepository, AccountingDocumentRepository, ArticleGroupRepository and ArticleRepository
    - CuttingSheet, CuttingSheetArticle, FenceModel, CuttingSheetArticleRepository,
      CuttingSheetRepository and list__last.
    - Material, MaterialProperty, MaterialSupplier, MaterialPropertyRepository, 
      MaterialRepository and MaterialSupplierRepository
    - CompanyInfo, Order, OrderMaterial, OrderMaterialProperty, Project, ProjectPriority, ProjectStatus, CompanyInfoRepository, OrderMaterialPropertyRepository, OrderMaterialRepository, OrderRepository, ProjectRepository
    - ArticleGroupRepository, ArticlePropertyRepository and ArticleRepository
    - ProjectNote, ProjectTask, ProjectTaskNote, ProjectTaskStatus, ProjectTaskType and PaymentRepository
  - Remove "doctrine/annotations" php package (2.0.2)
* 285 - After add a new material, duplicate material and edit material, back in Order edit mode

Bug:
* #288 - Recreate file name during upload to Project page
* #289 - Creating project from Proforma back Error

# Upgrade to 7.2.1
Enhancement:
* #285 - After add a new material, duplicate material and edit material, back in Order edit mode'

# Upgrade to 7.2.0
Upgrade:
* #279 - doctrine/cache (1.13.0 => 2.2.0)

# Upgrade to 7.1.0
Upgrade:
* #274 - symfony/http-foundation (5.4.48 => 6.1.12)
* #274 - symfony/cache (5.4.46 => 6.4.20)

# Upgrade to 7.0.3
Upgrade:
* #274 - symfony/http-foundation (5.4.16 => 5.4.48)
* #274 - symfony/cache (5.4.15 => 5.4.46)

# Upgrade to 7.0.2
Bug:
* #267 - Dont display images in projects
* #269 - After add a new article to Accounting document need to back to edit page

# Upgrade to 7.0.1
Bug:
* #264 - View Client from Accounting document error

# Upgrade to 7.0.0
Enhancement:
* #259 - Implement simple front controller
  * Change root namespace from "Roloffice" to "App"

# Upgrade to 6.8.1
Upgrade:
* #255 - Bootstrap (4.4.1 => 5.1.3)

Enhancement:
* #255 - Update forms with bootstrap5 classes:
  * clients
  * accounting documents
  * cutting sheet
  * materials
  * orders
  * articles
  * admin
  * projects

# Upgrade to 6.8.0
New version for new year 2025.

# Upgrade to 6.7.4
Environment requirements:
* PHP 8.2

# Upgrade to 6.7.3
Bug:
* #249 - Fatal error: Uncaught Error: Call to a member function getName() on null

Enhancement:
* Update "doctrine/annotations" package (1.14.2 => 1.14.4)
* Update "doctrine/annotations" package (1.14.4 => 2.0.2)
* Update "DoctrineORM" package (2.14.3 => 2.20.0)

# Upgrade to 6.7.2
Bug:
* #237 - Resolved issue with button "Create a new task" on project view page
* #240 - Add a new suppliers to the PrintOrder document

Enhancement:
* #231 - Update company info page and Company data on printed documents
* #235 - Nov system update
  * Update "DoctrineORM" package (2.13.5 => 2.14.3)
* Cleaning the code
* Fixing typo errors
 
# Upgrade to 6.7.1
Bug:
* #230 - Print Installation Record Issue

# Upgrade to 6.7.0
Enhancement:
* #227 - Update PHP to version 8.0 in project settings and on the Production environment 

# Upgrade to 6.6.3
Bug:
* #208 - Add possibility to delete order
* #215 - Table head for material suppliers

# Upgrade to 6.6.2
Enhancement:
* #139 - Table head for material suppliers
* #162 - Create a form for create and edit Article group

Bug:
* #203 - Warning: Undefined variable $client_street_name in .../form_advanced_search.php on line 102

# Upgrade to 6.6.1
Bug:
* #186 - Advanced search on Client section issue

Enhancement:
* #190 - Transaction note don't display in transactions listing

# Upgrade to 6.6.0
Enhancement:
* #198 - Add edit feature to Material on Order page

# Upgrade to 6.5.2

Bug:
* #194 - Error on Projects page'
* #197 - Update Deprecated Doctrine\ORM\Tools\Setup

Enhancement:
* On client preview page remove disabled attribute from client contacts
* On new client page set default country to "Srbija"
* Update "doctrine/orm" (2.12.4 => 2.13.5)

# Upgrade to 6.5.1

Bug:
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

Bug:
* #169 - Error on page create a new Project
* #170 - Error on Project page
* #171 - Error on page printProjectTask
* #173 - Error on page create a new Cutting
* #174 - Error on page view CuttingSheet

Enhancement:
* Getting Client data move from template to ClientRepository

# Upgrade to 6.4.1

Bug:
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

Bug:
* #154 - PHP issue

# Upgrade to 6.3.0

Enhancement:
* Create client form:
  * Fix typo
  * Remove value from default select option

Bug:
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

Feature:
* Add edit/delete form for AccountingDocument Transaction (payment)

# Upgrade to 6.0.4

Bug:
* #135 - On Order search, link to archived order is broken

# Upgrade to 6.0.3

Bug:
* #133 - Broken link from Project to Order

Enhancement:
* When perform advanced search repopulate search field

# Upgrade to 6.0.2

Bug:
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

Bug:
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

Bug:

* #101 - AccountingDocument Delete dont work
* #102 - Create AccDoc from Project page

Updates:
* Update "Doctrine/ORM" (2.8.4 => 2.8.5)

# Upgrade to 6.0.0-beta1

* Inplement Doctrine 2.8.4 to application.

# Upgrade to 6.0.0-alpha1

* Add Doctrine 2.8.4 application.
