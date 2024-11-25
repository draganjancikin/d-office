<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class CompanyInfoRepository extends EntityRepository
{
	/**
	 * Return all Company Info data inside associative array.
	 *
	 * @return array
	 */
	public function getCompanyInfoData($id): array {
		$company_info_data = $this->_em->find('\Roloffice\Entity\CompanyInfo', $id);
		if ($company_info_data->getCountry() === null) {
				$company_info_country = null;
		}
		else {
				$company_info_country = $this->_em->find('\Roloffice\Entity\Country', $company_info_data->getCountry());
		}
		if ($company_info_data->getCity() === null) {
				$company_info_city = null;
		}
		else {
				$company_info_city = $this->_em->find('\Roloffice\Entity\City', $company_info_data->getCity());
		}
		if ($company_info_data->getStreet() === null) {
				$company_info_street = null;
		}
		else {
				$company_info_street = $this->_em->find('\Roloffice\Entity\Street', $company_info_data->getStreet());
		}

		return [
			'name' => $company_info_data->getName(),
			'country' => $company_info_country ? $company_info_country->getName() : null,
			'city' => $company_info_city ? $company_info_city->getName() : null,
			'street' => $company_info_street ? $company_info_street->getName() : null,
			'home_number' => $company_info_data->getHomeNumber(),
			'pib' => $company_info_data->getPib(),
			'mb' => $company_info_data->getMb(),
			'bank_account_1' => $company_info_data->getBankAccount1(),
			'bank_account_2' => $company_info_data->getBankAccount2(),
			'phone_1' => $company_info_data->getPhone1(),
			'phone_2' => $company_info_data->getPhone2(),
			'email_1' => $company_info_data->getEmail1(),
			'email_2' => $company_info_data->getEmail2(),
			'website_1' => $company_info_data->getWebsite1(),
		];
	}
}