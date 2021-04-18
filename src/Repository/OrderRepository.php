<?php

namespace Roloffice\Repository;

use Doctrine\DBAL\Types\ObjectType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\AST\NewObjectExpression;

class OrderRepository extends EntityRepository {

  /**
   * Method that return number of Orders.
   *
   * @return int
   */
  public function getNumberOfOrders() {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('count(o.id)')
        ->from('Roloffice\Entity\Order','o');
    $count = $qb->getQuery()->getSingleScalarResult();
    return $count;
  }

  /**
   * Method that return last $limit Orders.
   * 
   * @return array
   */
  public function getLastOrders($limit = 5) {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('o')
        ->from('Roloffice\Entity\Order', 'o')
        ->orderBy('o.id', 'DESC')
        ->setMaxResults( $limit );
    $query = $qb->getQuery();
    $result = $query->getResult();
    return $result;
  }

  /**
   * Method that return all Materials on Order
   * 
   * @param int $order_id
   * 
   * @return array
   */
  public function getMaterialsOnOrder($order_id) {
    // Create a QueryBilder instance
    $qb = $this->_em->createQueryBuilder();
    $qb->select('om')
        ->from('Roloffice\Entity\OrderMaterial', 'om')
        ->join('om.material', 'm', 'om.material = m.id')
        ->where(
          $qb->expr()->eq('om.order', $order_id),
        )
        ->orderBy('om.id', 'ASC');
    $query = $qb->getQuery();
    $result = $query->getResult();
    
    return $result;
  }

  /**
   * @return object 
   */
  public function getProject($order_id) {
    $query = $this->_em->createQuery('SELECT p, o '
                                    . 'FROM Roloffice\Entity\Project p '
                                    . 'JOIN p.orders o '
                                    . 'WITH o.id = :order_id');
    $query->setParameter('order_id', $order_id);
    $projects = $query->getResult();
    return ($projects ? $projects[0] : NULL );
  }

  /**
   * Undocumented function
   *
   * @param int $order_id
   * @return void
   */
  public function setOrdinalNumInYear($order_id) {
    
    // count number of records in database table orders
    $order_count = $this->getNumberOfOrders();

    // get year of last order
    $year_of_last_order = $this->getLastOrder()->getCreatedAt()->format('Y');

    // get ordinal number in year of order before last
    $ordinal_number_of_order_before_last = $this->getOrderBeforeLast()->getOrdinalNumInYear();

    // year of order before last
    $year_of_order_before_last = $this->getOrderBeforeLast()->getCreatedAt()->format('Y');

    if($order_count ==0){  // prvi slučaj kada je tabela $table prazna
    
      return die("Table order is empty!");
    
    }elseif($order_count ==1){  // drugi slučaj - kada postoji jedan unos u tabeli $table
    
      $ordinal_number_in_year = 1; // pošto postoji samo jedan unos u tabelu $table $b_on dobija vrednost '1'
    
    }else{  // svi ostali slučajevi kada ima više od jednog unosa u tabeli $table
    
      if($year_of_last_order < $year_of_order_before_last){
        return die("Godina zadnjeg unosa je manja od godine predzadnjeg unosa! Verovarno datum nije podešen");
      }elseif($year_of_last_order == $year_of_order_before_last){ //nema promene godine
        $ordinal_number_in_year = $ordinal_number_of_order_before_last + 1;
      }else{  // došlo je do promene godine
        $ordinal_number_in_year = 1;
      }
    
    }

    // update ordinal_number_in_year
    $order = $this->_em->find('\Roloffice\Entity\Order', $order_id);

    if ($order === null) {
      echo "Order with ID $order_id does not exist.\n";
      exit(1);
    }

    $order->setOrdinalNumInYear($ordinal_number_in_year);

    $this->_em->flush();

  }

/**
   * Method that rerurn ID of last order in db table
   *
   * @return object
   */
  public function getLastOrder() {

    $qb = $this->_em->createQueryBuilder();
    $qb->select('o')
        ->from('Roloffice\Entity\Order', 'o')
        ->orderBy('o.id', 'DESC')
        ->setMaxResults(1);
    $query = $qb->getQuery();
    $last_order = $query->getResult()[0];
    
    return $last_order;
  }

  /**
   * Method that rerurn ID of last order in db table
   *
   * @return object
   */
  public function getOrderBeforeLast() {

    $qb = $this->_em->createQueryBuilder();
    $qb->select('o')
        ->from('Roloffice\Entity\Order', 'o')
        ->orderBy('o.id', 'DESC')
        ->setMaxResults(2);
    $query = $qb->getQuery();
    $order_before_last = $query->getResult()[1];
    
    return $order_before_last;
  }

  /**
   * Search method by criteria: Supplier name.
   * 
   * @return array
   */
  public function search($term) {
    // Create a QueryBilder instance
    $qb = $this->_em->createQueryBuilder();
    $qb->select('o')
      ->from('Roloffice\Entity\Order', 'o')
      ->join('o.supplier', 'supl', 'WITH', 'o.supplier = supl.id')
      ->where(
        $qb->expr()->like('supl.name', $qb->expr()->literal("%$term%")),
        )
      ->orderBy('o.id', 'DESC');
    $query = $qb->getQuery();
    $orders = $query->getResult();
    return $orders;
  }
}
