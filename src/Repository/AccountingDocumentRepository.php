<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class AccountingDocumentRepository extends EntityRepository {

    /**
     * Method that return number of AccountingDocuments with given AccountingDocumentType ID
     *
     * @param $type_id
     *
     * @return int
     */
    public function getNumberOfAccountingDocuments($type_id = NULL) {
        $qb = $this->getEntityManager()->createQueryBuilder();

        if ($type_id) {
            // If exist type_id query only AccountingDocument for given type_id
            $qb->select('count(ad.id)')
                ->from('App\Entity\AccountingDocument','ad')
                ->where(
                    $qb->expr()->eq('ad.type', $type_id),
                );
        }
        else {
            // If type_id dont exist query all Accounting Document
            $qb->select('count(ad.id)')
                ->from('App\Entity\AccountingDocument','ad');
        }
        $count = $qb->getQuery()->getSingleScalarResult();
        return $count;
    }

    /**
     * Method that return last AccountingDocuments
     *
     * @param int $type Type of AccountingDocument
     * @param int $limit Number of AccountingDocuments
     *
     * @return array
     */
    public function getLast($type, $isArchived, $limit) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('ad')
            ->from('App\Entity\AccountingDocument', 'ad')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->eq('ad.type', $type),
                    $qb->expr()->eq('ad.is_archived', $isArchived)
                )
            )
            ->orderBy('ad.id', 'DESC')
            ->setMaxResults( $limit );
        return $qb->getQuery()->getResult();
    }

    /**
     * Method that return all Articles on AccountingDocument
     *
     * @param int $ad_id AccountingDocument ID
     *
     * @return array
     */
    public function getArticles($ad_id) {
        // Create a QueryBilder instance
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('ada')
            ->from('App\Entity\AccountingDocumentArticle', 'ada')
            ->join('ada.article', 'a', 'ada.article = a.id')
            ->where(
                $qb->expr()->eq('ada.accounting_document', $ad_id),
            )
            ->orderBy('ada.id', 'ASC');
        $query = $qb->getQuery();
        $result = $query->getResult();

        return $result;
    }

    /**
     * Method that return advance payment by AccountingDocument.
     *
     * @param int $document_id
     *   AccountingDocument ID
     *
     * @return float
     */
    public function getAvans(int $document_id): float
    {
        // Get all payment for $accd_id where payment type = 1 or 2 (avans gotovinski, avans virmanski).
        $avans = 0;
        $payments = $this->getEntityManager()->find('\App\Entity\AccountingDocument', $document_id)->getPayments();
        foreach ($payments as $payment) {
            if ($payment->getType()->getId() == 1 || $payment->getType()->getId() == 2) {
                // Sabrati sve avanse.
                $avans = $avans + $payment->getAmount();
            }
        }
        return $avans;
    }

    /**
     * Method that return income by AccountingDocument
     *
     * @param int $accd_id
     *  AccountingDocument ID
     *
     * @return float
     */
    public function getIncome($accd_id) {
        // Get all payment for $accd_id where payment type = 3 or 4 (uplata gotovinska, uplata virmanska).
        $income = 0;
        $payments = $this->getEntityManager()->find('\App\Entity\AccountingDocument', $accd_id)->getPayments();
        foreach ($payments as $payment) {
            if ($payment->getType()->getId() == 3 || $payment->getType()->getId() == 4) {
                // Sabrati sve uplate.
                $income = $income + $payment->getAmount();
            }
        }
        return $income;
    }

    /**
     * Method that return previous AccountingDocument
     *
     * @param int $accd_id
     * @param int $accd_type_id
     *
     * @return object
     */
    public function getPrevious($accd_id, $accd_type_id){
        // Create a QueryBuilder instance.
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('ad')
            ->from('App\Entity\AccountingDocument', 'ad')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->lt('ad.id', $accd_id),
                    $qb->expr()->eq('ad.type', $accd_type_id)
                )
            )
            ->orderBy('ad.id', 'DESC');
        $query = $qb->getQuery();
        $result = $query->getResult();

        return $result ? $result[0] : null;
    }

    /**
     * Method that return next AccountingDocument
     *
     * @param int $accd_id
     * @param int $accd_type_id
     *
     * @return object
     */
    public function getNext($accd_id, $accd_type_id){
        // Create a QueryBuilder instance.
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('ad')
            ->from('App\Entity\AccountingDocument', 'ad')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->gt('ad.id', $accd_id),
                    $qb->expr()->eq('ad.type', $accd_type_id)
                )
            )
            ->orderBy('ad.id', 'ASC');
        $query = $qb->getQuery();
        $result = $query->getResult();

        return $result ? $result[0] : null;
    }

    /**
     * Method that set Ordinal AccountingDocument number in year for given AccountingDocument.
     *
     * @param int $acd_id
     *  AccountingDocument ID
     * @return void
     */
    public function setOrdinalNumInYear($acd_id) {
        // Given AccountingDocument.
        $acd = $this->getEntityManager()->find("\App\Entity\AccountingDocument", $acd_id);
        // Type of given AccountingDocument.
        $acd__type_id = $acd->getType()->getId();

        // Count number of records in database table v6__accounting_documents
        // for given AccountingDocumentType.
        $acd_count = $this->getNumberOfAccountingDocuments($acd__type_id);

        // Get year of last AccountingDocument.
        $year_of_last_acd = $this->getLastAccountingDocument()->getCreatedAt()->format('Y');

        // Get ordinal number in year of AccountingDocument before last with
        // same type_id.
        $ordinal_number_of_acd_before_last =
          $this->getAccountingDocumentBeforeLast($acd__type_id)
            ? $this->getAccountingDocumentBeforeLast($acd__type_id)->getOrdinalNumInYear()
            : 1;

        // Year of AccountingDocument before last.
        $year_of_acd_before_last = $this->getAccountingDocumentBeforeLast($acd__type_id)
            ? $this->getAccountingDocumentBeforeLast($acd__type_id)->getCreatedAt()->format('Y')
            : date('Y');

        if ($acd_count == 0) {  // prvi slučaj kada je tabela $table prazna
            return die("Table of AccountingDocument is empty!");
        }
        elseif ($acd_count == 1) {  // drugi slučaj - kada postoji jedan unos u tabeli $table
            $ordinal_number_in_year = 1; // pošto postoji samo jedan unos u tabelu $table $b_on dobija vrednost '1'
        }
        else {  // svi ostali slučajevi kada ima više od jednog unosa u tabeli $table
            if ($year_of_last_acd < $year_of_acd_before_last) {
                return die("Godina zadnjeg unosa je manja od godine predzadnjeg unosa! Verovarno datum nije podešen");
            }
            elseif ($year_of_last_acd == $year_of_acd_before_last) { //nema promene godine
                $ordinal_number_in_year = $ordinal_number_of_acd_before_last + 1;
            }
            else {  // došlo je do promene godine
                $ordinal_number_in_year = 1;
            }
        }

        // Update ordinal_number_in_year.
        $acd = $this->getEntityManager()->find('\App\Entity\AccountingDocument', $acd_id);

        if ($acd === null) {
            echo "AccountingDocument with ID $acd_id does not exist.\n";
            exit(1);
        }

        $acd->setOrdinalNumInYear($ordinal_number_in_year);

        $this->getEntityManager()->flush();
    }

    /**
     * Method that return ID of last AccountingDocument in db table
     *
     * @return object
     */
    public function getLastAccountingDocument() {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('ad')
            ->from('App\Entity\AccountingDocument', 'ad')
            ->orderBy('ad.id', 'DESC')
            ->setMaxResults(1);
        $query = $qb->getQuery();
        $result = $query->getResult();

        return $result[0] ?? null;
    }

    /**
     * Method that rerurn ID of AccountingDocument before last in db table for given AccountingDocumentType
     *
     * @param int $type_id
     *
     * @return object
     */
    public function getAccountingDocumentBeforeLast($type_id) {
        // Create a QueryBilder instance.
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('ad')
            ->from('App\Entity\AccountingDocument', 'ad')
            ->orderBy('ad.id', 'DESC')
            ->where(
                $qb->expr()->eq('ad.type', $type_id)
            )
            ->setMaxResults(2);
        $query = $qb->getQuery();

        if (count($query->getResult()) < 2) {
            return null;
        }

        return $query->getResult()[1];
    }

    /**
     * Total Debit Per Document
     * Ukupno zaduženje po dokumentu
     * Total Indebtedness In The Accounting Document
     * Ukupno zaduženje u računovodstvenom dokumentu
     *
     * @param int $accounting_document_id
     * @return array
     */
    public function getTotalAmountsByAccountingDocument($accounting_document_id) {
        // Get all Articles in AccountingDocument.
        $ad_articles = $this->getArticles($accounting_document_id);

        $total_tax_base = 0;
        $total_tax_amount = 0;

        foreach ($ad_articles as $ad_article) {
            // Get AccountingDocument Article quantity.
            $ad_a_quantity = $this->getEntityManager()
              ->getRepository('\App\Entity\AccountingDocumentArticle')
              ->getQuantity($ad_article->getId(), $ad_article->getArticle()->getMinCalcMeasure(), $ad_article->getPieces());

            $tax_base = $this->getEntityManager()
              ->getRepository('\App\Entity\AccountingDocumentArticle')
              ->getTaxBase($ad_article->getPrice(), $ad_article->getDiscount(), $ad_a_quantity);

            $tax_amount = $this->getEntityManager()
              ->getRepository('\App\Entity\AccountingDocumentArticle')
              ->getTaxAmount($tax_base, $ad_article->getTax() );

            $total_tax_base = $total_tax_base + $tax_base;
            $total_tax_amount = $total_tax_amount + $tax_amount;
        }
        return $total_tax_base + $total_tax_amount;
    }

    /**
     * Method that return last transactions
     *
     * @param int $limit
     *
     * @return array
     */
    public function getLastTransactions($limit){
        // Create a QueryBuilder instance.
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('p')
            ->from('App\Entity\Payment', 'p')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->eq('p.type', '1'),
                    $qb->expr()->eq('p.type', '2'),
                    $qb->expr()->eq('p.type', '3'),
                    $qb->expr()->eq('p.type', '4'),
                ),
            )
            ->orderBy('p.id', 'DESC')
            ->setMaxResults($limit);
        $query = $qb->getQuery();
        return $query->getResult();
    }

    /**
     * Method that return AccountingDocuent by Transaction.
     *
     * @param int $transaction_id
     *
     * @return object
     */
    public function getAccountingDocumentByTransaction($transaction_id) {
        // Create a Query.
        $query = $this->getEntityManager()
            ->createQuery('SELECT ad, p '
                        . 'FROM App\Entity\AccountingDocument ad '
                        . 'JOIN ad.payments p '
                        . 'WITH p.id = :payment_id');
        $query->setParameter('payment_id', $transaction_id);
        $accounting_documents = $query->getResult();
        return ($accounting_documents ? $accounting_documents[0] : NULL );
    }

    /**
     * Method that return all AccountingDocuments (pidbs) where client name or client name note
     * or pidb year ID like $name
     *
     * @param array $arr
     *
     * @return array
     */
    public function search($arr) {
        $type = $arr[0];
        $term = $arr[1];
        $is_archived = $arr[2];

        // Create a QueryBuilder instance.
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('ad')
            ->from('App\Entity\AccountingDocument', 'ad')
            ->join('ad.client', 'cl', 'WITH', 'ad.client = cl.id')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->orX(
                        $qb->expr()->like('cl.name', $qb->expr()->literal("%$term%")),
                        $qb->expr()->like('cl.name_note', $qb->expr()->literal("%$term%"))
                    ),
                    $qb->expr()->eq('ad.type', $type),
                    $qb->expr()->eq('ad.is_archived', $is_archived),
                )
            );
        $query = $qb->getQuery();
        return $query->getResult();
    }

    /**
     * Method that return Project by AccontingDocument
     *
     * @param int $acc_doc_id
     *
     * @return object
     */
    public function getProjectByAccountingDocument($acc_doc_id) {
        // Create a Query.
        $query = $this->getEntityManager()
            ->createQuery('SELECT p, acd '
                        . 'FROM App\Entity\Project p '
                        . 'JOIN p.accounting_documents acd '
                        . 'WITH acd.id = :acc_doc_id');
        $query->setParameter('acc_doc_id', $acc_doc_id);
        $project = $query->getResult();
        return ($project ? $project[0] : NULL );
    }

    /**
     * Method that return AccountingDocument Payments by PaymentTypes Income.
     *
     * @param int $acc_doc_id
     * @param array $payment_types
     *
     * @return array
     */
    public function getPaymentsByIncome($acc_doc_id) {
        $acc_doc = $this->getEntityManager()->find('\App\Entity\AccountingDocument', $acc_doc_id);
        $payments = $acc_doc->getPayments();
        foreach ($payments as $payment) {
            if ($payment->getType()->getId() == 3 || $payment->getType()->getId() == 4) {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Method that return AccountingDocument Payments by PaymentTypes Avans.
     *
     * @param int $acc_doc_id
     * @param array $payment_types
     *
     * @return array
     */
    public function getPaymentsByAvans($acc_doc_id) {
        $acc_doc = $this->getEntityManager()->find('\App\Entity\AccountingDocument', $acc_doc_id);
        $payments = $acc_doc->getPayments();
        foreach ($payments as $payment) {
            if ($payment->getType()->getId() == 1 || $payment->getType()->getId() == 2) {
                return TRUE;
            }
        }
        return FALSE;
    }

}
