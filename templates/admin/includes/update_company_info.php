<?php
// Update Company Info.
if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["updateCompanyInfo"])) {

  $name = $_POST["name"] ?? "";
  $pib = $_POST["pib"] ?? "";
  $mb = $_POST["mb"] ?? "";

  $country_id = $_POST["country_id"] ?? null;
  $country = $entityManager->find("\App\Entity\Country", $country_id);
  $city_id = $_POST["city_id"] ?? null;
  $city = $entityManager->find("\App\Entity\City", $city_id);
  $street_id = $_POST["street_id"] ?? null;
  $street = $entityManager->find("\App\Entity\Street", $street_id);

  $home_number = $_POST["home_number"] ?? "";
  $bank_account_1 = $_POST["bank_account_1"] ?? "";
  $bank_account_2 = $_POST["bank_account_2"] ?? "";
	$phone_1 = $_POST["phone_1"] ?? "";
	$phone_2 = $_POST["phone_2"] ?? "";
	$email_1 = $_POST["email_1"] ?? "";
	$email_2 = $_POST["email_2"] ?? "";
	$website_1 = $_POST["website_1"] ?? "";

  $company_info = $entityManager->find('\App\Entity\CompanyInfo', 1);

  $company_info->setName($name);
  $company_info->setPib($pib);
  $company_info->setMb($mb);
  $company_info->setCountry($country);
  $company_info->setCity($city);
  $company_info->setStreet($street);
  $company_info->setHomeNumber($home_number);
  $company_info->setBankAccount1($bank_account_1);
  $company_info->setBankAccount2($bank_account_2);
  $company_info->setPhone1($phone_1);
  $company_info->setPhone2($phone_2);
	$company_info->setEmail1($email_1);
	$company_info->setEmail2($email_2);
	$company_info->setWebsite1($website_1);
  $entityManager->flush();

  die('<script>location.href = "?companyInfo&view" </script>');
}
