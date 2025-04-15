<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class ArticlePropertyRepository
 */
class ArticlePropertyRepository extends EntityRepository
{

    /**
     * Method that return Article Properties ID in array.
     *
     * @param int $article_id
     *
     * @return array
     */
    public function getArticleProperties(int $article_id): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('ap')
            ->from('App\Entity\ArticleProperty', 'ap')
            ->where(
                $qb->expr()->eq('ap.article', $article_id)
            );
        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result;
    }

}