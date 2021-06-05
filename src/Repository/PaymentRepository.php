<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class PaymentRepository extends EntityRepository {

  /**
   * Method that return all avans payments by AccountingDocument ID
   * 
   * @param integer $accd_id
   *  AccountingDocument ID
   * 
   * @return float
   */
  public function getAvansIncome($accd_id){
    /*
    $result = $this->get("SELECT amount FROM payment WHERE pidb_id = '$pidb_id' AND (type_id = 1 OR type_id = 2) ");
    $avans = $this->sumAllValuesByKey($result, "amount");
    return $avans;
    */
    return 111.11;
  }

  

}
