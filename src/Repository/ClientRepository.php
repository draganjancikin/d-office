<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class ClientRepository extends EntityRepository {

  /**
   * Method that return number of clients
   *
   * @return int
   */
  public function getNumberOfClients() {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('count(c.id)');
    $qb->from('Roloffice\Entity\Client','c');
    $count = $qb->getQuery()->getSingleScalarResult();
    return $count;
  }

  /**
   * Method that return last $limit clients
   * 
   * @return 
   */
  public function getLastClients($limit = 0) {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('c')
        ->from('Roloffice\Entity\Client', 'c')
        ->orderBy('c.id', 'DESC')
        ->setMaxResults( $limit );
    $query = $qb->getQuery();
    $result = $query->getResult();
    // $array = $query->getArrayResult();
    return $result;
  }

}
