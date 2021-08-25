<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class ClientRepository extends EntityRepository {

  /**
   * Method that return total number of Clients
   *
   * @return int
   */
  public function getTotalNumberOfClients() {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('count(c.id)')
        ->from('Roloffice\Entity\Client','c');
    $count = $qb->getQuery()->getSingleScalarResult();
    return $count;
  }

  /**
   * Method that return last $limit clients
   * 
   * @return 
   */
  public function getLastClients($limit = 5) {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('c')
        ->from('Roloffice\Entity\Client', 'c')
        ->orderBy('c.id', 'DESC')
        ->setMaxResults( $limit );
    $query = $qb->getQuery();
    $result = $query->getResult();
    return $result;
  }

  /**
   * Search method by criteria: name and name note.
   * 
   * @param string $term
   * 
   * @return array
   */
  public function search($term) {
    // Create a QueryBilder instance
    $qb = $this->_em->createQueryBuilder();
    $qb->select('cl')
      ->from('Roloffice\Entity\Client', 'cl')
      ->join('cl.street', 's', 'WITH', 'cl.street = s.id')
      ->join('cl.city', 'c', 'WITH', 'cl.city = c.id')
      ->where(
        $qb->expr()->orX(
          $qb->expr()->like('cl.name', $qb->expr()->literal("%$term%")),
          $qb->expr()->like('cl.name_note', $qb->expr()->literal("%$term%"))
        )
      )
      ->orderBy('cl.name', 'ASC');

    $query = $qb->getQuery();
    $clients = $query->getResult();
    return $clients;
  }

  /**
   * Advanced search method that return all client with name or name_note like $term,
   * street like $street and city like $city.
   * 
   * @param string $term
   * @param string $street
   * @param string $cyty
   * 
   * @return array
   */
  public function advancedSearch($term, $street, $city) {
    // Create a QueryBilder instance.
  $qb = $this->_em->createQueryBuilder();
  
  $qb->select('cl')
      ->from('Roloffice\Entity\Client', 'cl')
      ->join('cl.street', 's', 'WITH', 'cl.street = s.id')
      ->join('cl.city', 'c', 'WITH', 'cl.city = c.id')
      ->where(
        $qb->expr()->orX(
          $qb->expr()->like('cl.name', $qb->expr()->literal("%$term%")),
          $qb->expr()->like('cl.name_note', $qb->expr()->literal("%$term%"))
        )
      )
      ->andWhere(
        $qb->expr()->like('s.name', $qb->expr()->literal("%$street%"))
      )
      ->andWhere(
        $qb->expr()->like('c.name', $qb->expr()->literal("%$city%"))
      ) 
      ->orderBy('cl.name', 'ASC');

  $query = $qb->getQuery();
  $clients_data = $query->getResult();

  return $clients_data;
  }

}
