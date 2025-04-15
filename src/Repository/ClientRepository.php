<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ClientRepository class.
 */
class ClientRepository extends EntityRepository
{

    /**
     * Method that return last $limit clients.
     *
     * @return array
     *   Array of clients.
     */
    public function getLastClients($limit = 5)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('c')
            ->from('App\Entity\Client', 'c')
            ->orderBy('c.id', 'DESC')
            ->setMaxResults($limit);
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
    public function search($term)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('cl')
            ->from('App\Entity\Client', 'cl')
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
     * @param string $city
     *
     * @return array
     */
    public function advancedSearch(string $term, string $street, string $city): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('cl')
            ->from('App\Entity\Client', 'cl')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->like('cl.name', $qb->expr()->literal("%$term%")),
                    $qb->expr()->like('cl.name_note', $qb->expr()->literal("%$term%"))
                )
            );

            if ($street <> '') {
                $qb->join('cl.street', 's', 'WITH', 'cl.street = s.id')
                    ->andWhere(
                        $qb->expr()->like('s.name', $qb->expr()->literal("%$street%"))
                    );
            }

            if ($city <> '') {
                $qb->join('cl.city', 'c', 'WITH', 'cl.city = c.id')
                    ->andWhere(
                        $qb->expr()->like('c.name', $qb->expr()->literal("%$city%"))
                    );
            }

        $qb->orderBy('cl.name', 'ASC');
        $query = $qb->getQuery();

        return $query->getResult();
    }

  /**
   *
   */
    public function checkGetClient($id = FALSE)
    {
        if ($id) {
            $new = preg_replace('/[^0-9]/', '', $id);
            return $new;
        }
        else {
            die('<script>location.href = "/clients/" </script>');
        }
    }

    /**
     * Return all client data inside associative array.
     *
     * @param $client_id
     * @return array
     */
    public function getClientData($client_id): array
    {
        $client_data = $this->getEntityManager()->find('\App\Entity\Client', $client_id);

        $client_type = $this->getEntityManager()->find('\App\Entity\ClientType', $client_data->getType());

        if ($client_data->getCountry() === null) {
            $client_country = null;
        }
        else {
            $client_country = $this->getEntityManager()->find('\App\Entity\Country', $client_data->getCountry());
        }

        if ($client_data->getCity() === null) {
            $client_city = null;
        }
        else {
            $client_city = $this->getEntityManager()->find('\App\Entity\City', $client_data->getCity());
        }

        if ($client_data->getStreet() === null) {
            $client_street = null;
        }
        else {
            $client_street = $this->getEntityManager()->find('\App\Entity\Street', $client_data->getStreet());
        }

        $client_contacts = $client_data->getContacts() ?? [];

        return [
            'id' => $client_data->getId(),
            'type_id' => $client_type->getId(),
            'type' => $client_type->getName(),
            'name' => $client_data->getName(),
            'name_note' => $client_data->getNameNote(),
            'country_id' => $client_country ? $client_country->getId() : null,
            'country' => $client_country ? $client_country->getName() : null,
            'city_id' => $client_city ? $client_city->getId() : null,
            'city' => $client_city ? $client_city->getName() : null,
            'street_id' => $client_street ? $client_street->getId() : null,
            'street' => $client_street ? $client_street->getName() : null,
            'home_number' => $client_data->getHomeNumber(),
            'address_note' => $client_data->getAddressNote(),
            'lb' => $client_data->getLb(),
            'is_supplier' => $client_data->getIsSupplier(),
            'note' => $client_data->getNote(),
            'contacts' => $client_contacts,
        ];
    }

}
