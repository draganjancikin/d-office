<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity (repositoryClass="App\Repository\CompanyInfoRepository")
 * @ORM\Table(name="v6__preferences")
 */
class CompanyInfo {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 * @var int
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", length=48)
	 * @var string
	 */
	protected $company_name;

	/**
	 * @ORM\Column(type="string", length=13)
	 * @var string
	 */
	protected $company_pib;

	/**
	 * @ORM\Column(type="string", length=8)
	 * @var string
	 */
	protected $company_mb;

	/**
	 * Meny Clients live in One Country.
	 * @ORM\ManyToOne(targetEntity="Country")
	 * @ORM\JoinColumn(name="company_country_id", referencedColumnName="id")
	 */
	protected $country;

	/**
	 * Meny Clients live in One City.
	 * @ORM\ManyToOne(targetEntity="City")
	 * @ORM\JoinColumn(name="company_city_id", referencedColumnName="id")
	 */
	protected $city;

	/**
	 * Meny Clients live in One Street.
	 * @ORM\ManyToOne(targetEntity="Street")
	 * @ORM\JoinColumn(name="company_street_id", referencedColumnName="id")
	 */
	protected $street;

	/**
	 * @ORM\Column(type="string", length=8)
	 * @var string
	 */
	protected $company_home_number;

	/**
	 * @ORM\Column(type="string", length=128)
	 * @var string
	 */
	protected $company_bank_account_1;

	/**
	 * @ORM\Column(type="string", length=128)
	 * @var string
	 */
	protected $company_bank_account_2;

	/**
	 * @ORM\Column(type="string", length=128)
	 * @var string
	 */
	protected $company_phone_1;

	/**
	 * @ORM\Column(type="string", length=128)
	 * @var string
	 */
	protected $company_phone_2;

	/**
	 * @ORM\Column(type="string", length=128)
	 * @var string
	 */
	protected $company_email_1;

	/**
	 * @ORM\Column(type="string", length=128)
	 * @var string
	 */
	protected $company_email_2;

	/**
	 * @ORM\Column(type="string", length=128)
	 * @var string
	 */
	protected $company_website_1;


	public function getId()
    {
        return $this->id;
    }

	public function setName($company_name)
	{
			$this->company_name = $company_name;
	}

	public function getName()
	{
			return $this->company_name;
	}

	public function setPib($company_pib)
	{
			$this->company_pib = $company_pib;
	}

	public function getPib()
	{
			return $this->company_pib;
	}

	public function setMb($company_mb)
	{
			$this->company_mb = $company_mb;
	}

	public function getMb()
	{
			return $this->company_mb;
	}

	public function setCountry($country)
	{
			$this->country = $country;
	}

	public function getCountry()
	{
			return $this->country;
	}

	public function setCity($city)
	{
			$this->city = $city;
	}

	public function getCity()
	{
			return $this->city;
	}

	public function setStreet($street)
	{
			$this->street = $street;
	}

	public function getStreet()
	{
			return $this->street;
	}

	public function setHomeNumber($company_home_number)
	{
			$this->company_home_number = $company_home_number;
	}

	public function getHomeNumber()
	{
			return $this->company_home_number;
	}

	public function setBankAccount1($company_bank_account_1)
	{
			$this->company_bank_account_1 = $company_bank_account_1;
	}

	public function getBankAccount1()
	{
			return $this->company_bank_account_1;
	}

	public function setBankAccount2($company_bank_account_2)
	{
			$this->company_bank_account_2 = $company_bank_account_2;
	}

	public function getBankAccount2()
	{
			return $this->company_bank_account_2;
	}

	public function setPhone1($company_phone_1)
	{
		$this->company_phone_1 = $company_phone_1;
	}

	public function getPhone1()
	{
		return $this->company_phone_1;
	}

	public function setPhone2($company_phone_2)
	{
		$this->company_phone_2 = $company_phone_2;
	}

	public function getPhone2()
	{
		return $this->company_phone_2;
	}

	public function setEmail1($company_email_1)
	{
		$this->company_email_1 = $company_email_1;
	}

	public function getEmail1()
	{
		return $this->company_email_1;
	}

	public function setEmail2($company_email_2)
	{
		$this->company_email_2 = $company_email_2;
	}

	public function getEmail2()
	{
		return $this->company_email_2;
	}

	public function setWebsite1($company_website_1)
	{
		$this->company_website_1 = $company_website_1;
	}

	public function getWebsite1()
	{
		return $this->company_website_1;
	}
}