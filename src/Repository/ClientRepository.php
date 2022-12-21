<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class ClientRepository extends EntityRepository {
  
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
      ->leftJoin('cl.street', 's', 'WITH', 'cl.street = s.id')
      ->leftJoin('cl.city', 'c', 'WITH', 'cl.city = c.id')
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
        // Create a QueryBuilder instance.
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
        return $query->getResult();
    }

    /**
     *
     */
    public function checkGetClient($id = FALSE){
        if ($id) {
            $new = preg_replace('/[^0-9]/', '', $id);
            return $new;
        } else {
            die('<script>location.href = "/clients/" </script>');
        }
    }

    /**
     * Return all client data inside associative array.
     *
     * @param $client_id
     * @return array
     */
    public function getClientData($client_id): array {
        $client_data = $this->_em->find('\Roloffice\Entity\Client', $client_id);
        $client_type = $this->_em->find('\Roloffice\Entity\ClientType', $client_data->getType());
        if ($client_data->getCountry() === null) {
            $client_country = null;
        } else {
            $client_country = $this->_em->find('\Roloffice\Entity\Country', $client_data->getCountry());
        }
        if ($client_data->getCity() === null) {
            $client_city = null;
        } else {
            $client_city = $this->_em->find('\Roloffice\Entity\City', $client_data->getCity());
        }
        if ($client_data->getStreet() === null) {
            $client_street = null;
        } else {
            $client_street = $this->_em->find('\Roloffice\Entity\Street', $client_data->getStreet());
        }
        $client_contacts = $client_data->getContacts();
        return [
            'id' => $client_data->getId(),
            'type_id' => $client_type->getId(),
            'type' => $client_type->getName(),
            'name' => $client_data->getName(),
            'name_note' => $client_data->getNameNote(),
            'country_id' => $client_country->getId(),
            'country' => $client_country->getName(),
            'city_id' => $client_city->getId(),
            'city' => $client_city->getName(),
            'street_id' => $client_street->getId(),
            'street' => $client_street->getName(),
            'home_number' => $client_data->getHomeNumber(),
            'address_note' => $client_data->getAddressNote(),
            'lb' => $client_data->getLb(),
            'is_supplier' => $client_data->getIsSupplier(),
            'note' => $client_data->getNote(),
            'contacts' => $client_contacts,
        ];
    }

}
