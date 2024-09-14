<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class CuttingSheetRepository extends EntityRepository {

  /**
   * Method that return number of CuttingSheets
   *
   * @return int
   */
  public function getNumberOfCuttingSheets() {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('count(cs.id)')
        ->from('Roloffice\Entity\CuttingSheet','cs');
    $count = $qb->getQuery()->getSingleScalarResult();
    return $count;
  }

  /**
   * Method that return last $limit CuttingSheets
   * 
   * @return 
   */
  public function getLastCuttingSheets($limit = 5) {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('cs')
        ->from('Roloffice\Entity\CuttingSheet', 'cs')
        ->orderBy('cs.id', 'DESC')
        ->setMaxResults( $limit );
    $query = $qb->getQuery();
    $result = $query->getResult();
    return $result;
  }

  /**
   * Method that return all Articles on CuttingSheet
   * 
   * @param int $cutting_sheet_id
   * 
   * @return array
   */
  public function getArticlesOnCuttingSheet($cutting_sheet_id) {
    // Create a QueryBilder instance
    $qb = $this->_em->createQueryBuilder();
    $qb->select('csa')
        ->from('Roloffice\Entity\CuttingSheetArticle', 'csa')
        ->join('csa.fence_model', 'fm', 'csa.fence_model = fm.id')
        ->where(
          $qb->expr()->eq('csa.cutting_sheet', $cutting_sheet_id),
        )
        ->orderBy('csa.id', 'ASC');
    $query = $qb->getQuery();
    $result = $query->getResult();
    
    return $result;
  }

    /**
     * Method that set ordinal number in year of CuttingSheet
     *
     * @param int $cutting_sheet_id
     *
     * @return void
     */
    public function setOrdinalNumInYear($cutting_sheet_id) {
        // Count number of records in database table v6_cutting_sheets.
        $order_count = $this->getNumberOfCuttingSheets();

        $year_of_last_order = $this->getLastCuttingSheet()->getCreatedAt()->format('Y');
        $ordinal_number_of_order_before_last = $this->getCuttingSheetBeforeLast() ? $this->getCuttingSheetBeforeLast()->getOrdinalNumInYear() : 1;
        $year_of_order_before_last = $this->getCuttingSheetBeforeLast() ? $this->getCuttingSheetBeforeLast()->getCreatedAt()->format('Y') : date('Y');

        if ($order_count ==0) {  // First case - table $table is empty.
            return die("Table order is empty!");
        } elseif ($order_count ==1){  // Second case - kada postoji jedan unos u tabeli $table.
            $ordinal_number_in_year = 1; // Pošto postoji samo jedan unos u tabelu $table $b_on dobija vrednost '1'.
        } else {  // Svi ostali slučajevi kada ima više od jednog unosa u tabeli $table.

            if ($year_of_last_order < $year_of_order_before_last){
                return die("Godina zadnjeg unosa je manja od godine predzadnjeg unosa! Verovarno datum nije podešen");
            } elseif ($year_of_last_order == $year_of_order_before_last){ // Nema promene godine.
                $ordinal_number_in_year = $ordinal_number_of_order_before_last + 1;
            } else {  // Došlo je do promene godine.
                $ordinal_number_in_year = 1;
            }
        }

        // Update ordinal_number_in_year.
        $cutting_sheet = $this->_em->find('\Roloffice\Entity\CuttingSheet', $cutting_sheet_id);
        if ($cutting_sheet === null) {
            echo "Order with ID $cutting_sheet_id does not exist.\n";
            exit(1);
        }
        $cutting_sheet->setOrdinalNumInYear($ordinal_number_in_year);
        $this->_em->flush();
    }

  /**
   * Method that return last CuttingSheet in db table.
   *
   * @return object
   */
  public function getLastCuttingSheet() {

    $qb = $this->_em->createQueryBuilder();
    $qb->select('cs')
        ->from('Roloffice\Entity\CuttingSheet', 'cs')
        ->orderBy('cs.id', 'DESC')
        ->setMaxResults(1);
    $query = $qb->getQuery();
    $last__cutting_sheet = $query->getResult()[0];
    
    return $last__cutting_sheet;
  }

    /**
     * Method that return before last CuttingSheet in db table
     *
     * @return object
     */
    public function getCuttingSheetBeforeLast() {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('cs')
            ->from('Roloffice\Entity\CuttingSheet', 'cs')
            ->orderBy('cs.id', 'DESC')
            ->setMaxResults(2);
        $query = $qb->getQuery();
        if (count($query->getResult()) < 2) {
            return null;
        }
        return $query->getResult()[1];
    }

  /**
   * Search method by criteria: Client name.
   * 
   * @param string $term
   * 
   * @return array
   */
  public function search($term) {
    // Create a QueryBilder instance
    $qb = $this->_em->createQueryBuilder();
    $qb->select('cs')
      ->from('Roloffice\Entity\CuttingSheet', 'cs')
      ->join('cs.client', 'cl', 'WITH', 'cs.client = cl.id')
      ->where(
        $qb->expr()->like('cl.name', $qb->expr()->literal("%$term%")),
        )
      ->orderBy('cs.id', 'DESC');
    $query = $qb->getQuery();
    $cutting_sheet = $query->getResult();
    return $cutting_sheet;
  }

}
