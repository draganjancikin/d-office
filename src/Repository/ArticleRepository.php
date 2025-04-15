<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class ArticleRepository
 */
class ArticleRepository extends EntityRepository
{

    /**
     * Method that return all Articles sort by name ASC
     *
     * @return array
     */
    public function findAll(): array
    {
        return $this->findBy(array(), array('name' => 'ASC'));
    }

    /**
     * Method that return last $limit Articles
     *
     * @param int $limit
     *
     * @return array
     */
    public function getLastArticles(int $limit = 5): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('a')
            ->from('App\Entity\Article', 'a')
            ->orderBy('a.id', 'DESC')
            ->setMaxResults( $limit );
        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result;
    }

    /**
     * Method that return all Article in ArticleGroup by $group_id.
     *
     * @param int $group_id
     *
     * @return array
     */
    public function getArticlesByGroup(int $group_id): array
    {
        // Create a QueryBuilder instance.
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('a')
            ->from('App\Entity\Article', 'a')
            ->where(
                $qb->expr()->eq('a.group', $group_id),
            )
            ->orderBy('a.name', 'ASC');
        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result;
    }

    /**
     * Method that return all Article where name $term.
     *
     * @param string $term
     *
     * @return array
     */
    public function search(string $term): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('a')
            ->from('App\Entity\Article', 'a')
            ->where(
                $qb->expr()->like('a.name', $qb->expr()->literal("%$term%")),
            )
            ->orderBy('a.name', 'ASC');
        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result;
    }

}
