<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Payment repository.
 *
 * @package App\Repository
 */
class PaymentRepository extends EntityRepository
{

    /**
     * Method that return all avans payments by AccountingDocument ID
     *
     * @param integer $accd_id
     *  AccountingDocument ID
     *
     * @return float
     */
    public function getAvansIncome(int $accd_id): float
    {

        /*
        $result = $this->get("SELECT amount FROM payment WHERE pidb_id = '$pidb_id' AND (type_id = 1 OR type_id = 2) ");
        $avans = $this->sumAllValuesByKey($result, "amount");
        return $avans;
        */
        return 111.11;
    }

    /**
     *
     */
    public function getDailyCashTransactions($date = '')
    {
        if (!$date) {
          $date = date('Y-m-d');
        }

        $from = new \DateTime($date . " 00:00:00");
        $to   = new \DateTime($date . " 23:59:59");

        // Create a QueryBilder instance.
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('p')
            ->from('App\Entity\Payment', 'p')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->between('p.created_at', ':from', ':to'),
                    $qb->expr()->orX(
                        $qb->expr()->eq('p.type', '1'),
                        $qb->expr()->eq('p.type', '3'),
                        $qb->expr()->eq('p.type', '5'),
                        $qb->expr()->eq('p.type', '6'),
                        $qb->expr()->eq('p.type', '7')
                    )
                )
            )
            ->setParameter('from', $from )
            ->setParameter('to', $to)
            ->orderBy('p.id', 'ASC');
        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result;

    /*
        ->where(
          $qb->expr()->andX(
            $qb->expr()->between('p.created_at', "$date 00:00:00", "$date 23:59:59"),
            $qb->expr()->orX(
              $qb->expr()->eq('p.type', '1'),
              $qb->expr()->eq('p.type', '3'),
              $qb->expr()->eq('p.type', '5'),
              $qb->expr()->eq('p.type', '6'),
              $qb->expr()->eq('p.type', '7'),
            ),

          )
        )
      */


      /*
          $result = $this->get("SELECT * "
                              ."FROM $this->transaction_table "
                              ."WHERE (created_at_date BETWEEN '$date 00:00:00' AND '$date 23:59:59') AND (type_id = 1 || type_id = 3 || type_id = 5 || type_id = 6 || type_id = 7) "
                              ."ORDER BY date ASC;");
          $i = 0;
          foreach($result as $row){
              switch ($row['type_id']) {
                  case 1:
                      $type = "Avans (gotovinski)";
                      break;
                  case 2:
                      $type = "Avans";
                      break;
                  case 3:
                      $type = "Uplata (gotovinska)";
                      break;
                  case 4:
                      $type = "Uplata";
                      break;
                  case 5:
                      $type = "Početno stanje kase";
                      break;
                  case 6:
                      $type = "Izlaz gotovine na kraju dana (smene)";
                      break;
                  case 7:
                      $type = "Izlaz gotovine";
                      break;
                  default:
                  $type = "_";
                      break;
              }
              if ( $row['pidb_id'] <> 0 ) {
                  $pidb_data = $this->getPidb($row['pidb_id']);
                  $result[$i]['pidb_y_id'] = $pidb_data['y_id'];
                  $result[$i]['client_name'] = $pidb_data['client_name'];
                  $result[$i]['pidb_title'] = $pidb_data['title'];
              } else {
                  $result[$i]['pidb_y_id'] = 0;
                  $result[$i]['client_name'] = "";
                  $result[$i]['pidb_title'] = "";
              }

              $result[$i]['type_name'] = $type;
              $i++;
          }
          return $result;
      */

      // return [];
    }

    /**
     *
     */
    public function getDailyCashSaldo($date = '') {
        if (!$date) {
            $date = date('Y-m-d');
        }

        $from = new \DateTime($date . " 00:00:00");
        $to   = new \DateTime($date . " 23:59:59");

        // Create a QueryBilder instance.
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('SUM(p.amount)')
            ->from('App\Entity\Payment', 'p')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->between('p.created_at', ':from', ':to'),
                    $qb->expr()->orX(
                        $qb->expr()->eq('p.type', '1'),
                        $qb->expr()->eq('p.type', '3'),
                        $qb->expr()->eq('p.type', '5'),
                        $qb->expr()->eq('p.type', '6'),
                        $qb->expr()->eq('p.type', '7')
                    )
                )
            )
            ->setParameter('from', $from )
            ->setParameter('to', $to);
        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result[0][1];

        /*

          $result = $this->get("SELECT SUM(amount) "
                              ."FROM $this->transaction_table "
                              ."WHERE (created_at_date BETWEEN '$date 00:00:00' AND '$date 23:59:59') AND (type_id = 1 || type_id = 3 || type_id = 5 || type_id = 6)");
          return $result[0]['SUM(amount)'];

        */
    }

    /**
     * Method that check if exist first cash input in current day.
     *
     * @return bool
     */
    public function ifExistFirstCashInput(): bool
    {
        $from = new \DateTime(date('Y-m-d') . " 00:00:00");
        $to   = new \DateTime(date('Y-m-d') . " 23:59:59");
        // Create a QueryBuilder instance.
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('p')
            ->from('App\Entity\Payment', 'p')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->eq('p.type', '5'),
                    $qb->expr()->between('p.created_at', ':from', ':to'),
                ),
            )
            ->setParameter('from', $from )
            ->setParameter('to', $to);
        $query = $qb->getQuery();
        $result = $query->getResult();
        if (empty($result)) {
            return false;
        }
        return true;
    }

}
