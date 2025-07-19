<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * EmployeeRepository class.
 */
class EmployeeRepository extends EntityRepository
{

    /**
     * Method that return last $limit Employees.
     *
     * @return array
     *   Array of Employees.
     */
    public function getLastEmployees($limit = 5)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('e')
            ->from('App\Entity\Employee', 'e')
            ->orderBy('e.id', 'DESC')
            ->setMaxResults($limit);
        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result;
    }

    /**
     * Search method by criteria: name.
     *
     * @param string $term
     *
     * @return array
     */
    public function search(string $term): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('e')
            ->from('App\Entity\Employee', 'e')
            ->where(
                $qb->expr()->like('e.name', $qb->expr()->literal("%$term%")),
            )
            ->orderBy('e.name', 'ASC');

        $query = $qb->getQuery();
        $employees = $query->getResult();
        return $employees;
    }

}
