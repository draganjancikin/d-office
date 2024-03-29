<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class ProjectRepository extends EntityRepository {

    /**
     * Method that return number of Projects.
     *
     * @return int
     */
    public function getNumberOfProjects() {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('count(p.id)')
            ->from('Roloffice\Entity\Project','p');
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Method that return all active Projects.
     *
     * @return array
     */
    public function getAllActiveProjects() {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('p')
            ->from('Roloffice\Entity\Project','p')
            ->where(
                $qb->expr()->eq('p.status', 1),
            );
        $query = $qb->getQuery();
        return $query->getResult();
    }

    /**
     * Method that set OrdinalProject number in year.
     *
     * @param int $project_id
     * @return void
     */
    public function setOrdinalNumInYear($project_id) {
        // Count number of records in database table project.
        $project_count = $this->getNumberOfProjects();

        $id_of_last_project = $this->getLastProject()->getId();
        $year_of_last_project = $this->getLastProject()->getCreatedAt()->format('Y');

        // @HOLMES - This variable dont use anywhere. Probably need delete.
        // $id_of_project_before_last = $this->getProjectBeforeLast()->getId();

        $ordinal_number_of_project_before_last = $this->getProjectBeforeLast() ? $this->getProjectBeforeLast()->getOrdinalNumInYear() : 1;
        $year_of_project_before_last = $this->getProjectBeforeLast()
            ? $this->getProjectBeforeLast()->getCreatedAt()->format('Y')
            : date('Y');

        if ($project_count ==0){  // First case - kada je tabela $table prazna.
            return die("Tabela project je prazna!");
        } elseif ($project_count ==1) {  // Second case - kada postoji jedan unos u tabeli $table.
            $ordinal_number_in_year = 1; // Pošto postoji samo jedan unos u tabelu $table $b_on dobija vrednost '1'.
        } else {  // Svi ostali slučajevi kada ima više od jednog unosa u tabeli $table.
    
            if ($year_of_last_project < $year_of_project_before_last){
                return die("Godina zadnjeg unosa je manja od godine predzadnjeg unosa! Verovarno datum nije podešen");
            } elseif ($year_of_last_project == $year_of_project_before_last){ // Nema promene godine.
                $ordinal_number_in_year = $ordinal_number_of_project_before_last + 1;
            } else {  // Došlo je do promene godine.
                $ordinal_number_in_year = 1;
            }
        }

        // update ordinal_number_in_year
        $project = $this->_em->find('\Roloffice\Entity\Project', $project_id);

        if ($project === null) {
            echo "Project with ID $project_id does not exist.\n";
            exit(1);
        }

        $project->setOrdinalNumInYear($ordinal_number_in_year);
        $this->_em->flush();
    }

    /**
     * Method that rerurn ID of last project in db table
     *
     * @return object
     */
    public function getLastProject() {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('p')
            ->from('Roloffice\Entity\Project', 'p')
            ->orderBy('p.id', 'DESC')
            ->setMaxResults(1);
        $query = $qb->getQuery();
        return $query->getResult()[0];
    }

    /**
     * Method that rerurn ID of Project before last in db table
     *
     * @return object
     */
    public function getProjectBeforeLast() {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('p')
            ->from('Roloffice\Entity\Project', 'p')
            ->orderBy('p.id', 'DESC')
            ->setMaxResults(2);
        $query = $qb->getQuery();
        if (count($query->getResult()) < 2) {
            return null;
        }
        return $query->getResult()[1];
    }

    /**
     * Method that return array of Cities
     *
     * @return array
     */
    public function getCitiesByActiveProject() {
        $qb = $this->_em->createQueryBuilder();

        /*
        $qb->select('p')
            ->from('Roloffice\Entity\Project','p')
            ->join('p.client', 'cl', 'WITH', 'p.client = cl.id')
            ->join('cl.city', 'ci', 'WITH', 'cl.city = ci.id')
            ->where(
                $qb->expr()->eq('p.status', 1)
            )
            ->orderBy('ci.name', 'ASC');
        */

        $qb->select('ci.id, ci.name')
            ->from('Roloffice\Entity\Project','p')
            ->join('p.client', 'cl', 'WITH', 'p.client = cl.id')
            ->join('cl.city', 'ci', 'WITH', 'cl.city = ci.id')
            ->where(
                $qb->expr()->eq('p.status', 1)
            )
            ->orderBy('ci.name', 'ASC')
            ->distinct();
      
        $query = $qb->getQuery();
        return $query->getResult();
    }

    /**
     * Methot that return all Project with status = $status.
     *
     * @param int $status
     *
     * @return array
     */
    public function projectTracking ($status){
        $qb = $this->_em->createQueryBuilder();
        $qb->select('p')
            ->from('Roloffice\Entity\Project','p')
            ->where(
                $qb->expr()->eq('p.status', "$status")
            )
            ->orderBy('p.id', 'ASC');
        $query = $qb->getQuery();
        return $query->getResult();
    }

    /**
     * Methot thar return all Tasks by Project
     */
    public function projectTasks($project_id){
        $qb = $this->_em->createQueryBuilder();
        $qb->select('pt')
            ->from('Roloffice\Entity\ProjectTask','pt')
            ->where(
                $qb->expr()->eq("pt.project", "$project_id")
            )
            ->orderBy('pt.created_at', 'ASC');
        $query = $qb->getQuery();
        return $query->getResult();
    }

    /**
     *
     */
    public function projectTrackingByCity($status, $city_id) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('p')
            ->from('Roloffice\Entity\Project','p')
            ->join('p.client', 'cl', 'WITH', 'p.client = cl.id')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->eq("p.status", "$status"),
                    $qb->expr()->eq("cl.city", "$city_id")
                )
            );
        $query = $qb->getQuery();
        return $query->getResult();
    }

    /**
     * Method that return Project search
     *
     * @param string $term
     *
     * @return array
     */
    public function search($term) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('p')
            ->from('Roloffice\Entity\Project','p')
            ->join('p.client', 'cl', 'WITH', 'p.client = cl.id')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->like('cl.name', $qb->expr()->literal("%$term%")),
                    $qb->expr()->like('cl.name_note', $qb->expr()->literal("%$term%")),
                    $qb->expr()->like('p.title', $qb->expr()->literal("%$term%"))
                )
            );
        $query = $qb->getQuery();
        return $query->getResult();
    }

    /**
     * Method that return all project with client name or client name note like $client
     * and project title like $title and client city like $city.
     *
     * @param string $client
     *  Client name.
     * @param string $title
     *  Project title.
     * @param string $city
     *  City name.
     *
     * @return array
     */
    public function advancedSearch($client, $title, $city) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('p')
            ->from('Roloffice\Entity\Project','p')
            ->join('p.client', 'cl', 'WITH', 'p.client = cl.id')
            ->join('cl.city', 'ci', 'WITH', 'cl.city = ci.id')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->orX(
                        $qb->expr()->like('cl.name', $qb->expr()->literal("%$client%")),
                        $qb->expr()->like('cl.name_note', $qb->expr()->literal("%$client%")),
                    ),
                    $qb->expr()->like('p.title', $qb->expr()->literal("%$title%")),
                    $qb->expr()->like('ci.name', $qb->expr()->literal("%$city%")),
                )
            )
            ->orderBy('p.ordinal_num_in_year', 'ASC');
        $query = $qb->getQuery();
        return $query->getResult();
    }

    /**
     * Method that return Notes by Project
     *
     * @param int $project_id
     *
     * @return array
     */
    public function getNotesByProject($project_id) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('pn')
            ->from('Roloffice\Entity\ProjectNote','pn')
            ->where(
                $qb->expr()->eq("pn.project", "$project_id")
            )
            ->orderBy('pn.id', 'ASC');;
        $query = $qb->getQuery();
        return $query->getResult();
    }

}
