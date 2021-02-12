<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 font-weight-bold text-primary">Zadnji upisani klijenti</h6>
  </div>
  <div class="card-body p-2">
    <!-- table with list of last client -->
    <div class="table-responsive">
      <table class="table table-bordered table-hover" id="" width="100%" cellspacing="0">
        <thead class="thead-light"><tr><th>klijent</th><th>adresa</th></tr></thead>
        <tfoot class="thead-light"><tr><th>klijent</th><th>adresa</th></tr></tfoot>
        <tbody>
          <?php
          $last_clients = $entityManager->getRepository('\Roloffice\Entity\Client')->getLastClients(10);
          foreach ($last_clients as $client):
            $country = $entityManager->find('\Roloffice\Entity\Country', $client->getCountry() );
            $city = $entityManager->find('\Roloffice\Entity\City', $client->getCity() );
            $street = $entityManager->find('\Roloffice\Entity\Street', $client->getStreet() );
            ?>
            <tr>
              <td><a href="?view&client_id=<?php echo $client->getId() ?>"><?php echo $client->getName() ?></a></td>
              <td><?php echo ( $street->getName() == "" ? "" : $street->getName() . " " . $client->getHomeNumber() .  ", " ) . $city->getName(). ', ' .$country->getName() ?></td>
            </tr>
            <?php
          endforeach;
          ?>
        </tbody>
      </table>
    </div>
  </div>
  <!-- End of Card body -->
</div>
