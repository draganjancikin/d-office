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

}
