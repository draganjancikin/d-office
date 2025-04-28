<?php

namespace App\Core;

/**
 * EntityManagerFactory class.
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class EntityManagerFactory {

    private static ?\Doctrine\ORM\EntityManager $entityManager = null;

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public static function getEntityManager(): \Doctrine\ORM\EntityManager {
        if (self::$entityManager === null) {
            self::$entityManager = require __DIR__ . '/../../config/packages/doctrine.php';
        }

        return self::$entityManager;
    }

}
