<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

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

}
