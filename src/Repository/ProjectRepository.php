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
    $count = $qb->getQuery()->getSingleScalarResult();
    return $count;
  }

  /**
   * 
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
    $result = $query->getResult();
    
    return $result;
  }

}


/*
$project = array();
        $projects = array();
        
        // listamo sve projekte koji nisu arhivirani, tj, status <> '9'
        $result = $this->connection->query("SELECT project.id, project.pr_id, project.date, project.title, project.status, project.client_id, v6_clients.name "
                                        . "FROM project "
                                        . "JOIN v6_clients "
                                        . "ON project.client_id = v6_clients.id "
                                        . "WHERE status <> '9' AND status = $status "
                                        ." ORDER BY project.id") or die(mysqli_error($this->connection));
        while($row_project = $result->fetch_assoc()):
            $project_date = date('Y-m-d',strtotime($row_project['date']));
            $style = $this->style($project_date);
            $client_id = $row_project['client_id'];
            
            // pozivanje funkcije koja vraća naziv naselja za klijenta $client_id
            $client_city_name = self::getCityName($client_id);

            $client_name = $row_project['name'];

            $project = array(
                'id' => $row_project['id'],
                'pr_id' => $row_project['pr_id'],
                'date' => $project_date,
                'style' => $style,
                'title' => $row_project['title'],
                'status' => $row_project['status'],
                'client_name' => $client_name,
                'client_city_name' => $client_city_name

            );
            array_push($projects, $project);
        endwhile;

        return $projects;
*/
