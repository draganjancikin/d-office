<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class ArticleGroupRepository extends EntityRepository
{

    /**
     * Method that return Article Groups.
     *
     * @return array
     */
    public function getArticleGroups() {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('ag')
            ->from('App\Entity\ArticleGroup', 'ag')
            ->where(
                $qb->expr()->neq('ag.id', '1')
            )
          ->orderBy('ag.name', 'ASC');
        $query = $qb->getQuery();
        $result = $query->getResult();
      return $result;
    }

}