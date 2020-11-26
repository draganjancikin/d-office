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
          $last_clients = $client->getLastClients(10);
          foreach ($last_clients as $client):
            ?>
            <tr>
              <td><a href="?view&client_id=<?php echo $client['id'] ?>"><?php echo $client['name'] ?></a></td>
              <td><?php echo ( $client['street_name'] == "" ? "" : $client['street_name'] . " " . $client['home_number'] .  ", " ) . $client['city_name']. ', ' .$client['state_name']; ?></td>
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