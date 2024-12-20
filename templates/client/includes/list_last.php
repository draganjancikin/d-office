<div class="col-lg-12 col-xl-10 px-2">
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
                    $last_clients = $entityManager->getRepository('\App\Entity\Client')->getLastClients(10);
                    foreach ($last_clients as $client):
                        ?>
                        <tr>
                            <td><a href="?view&client_id=<?php echo $client->getId() ?>"><?php echo $client->getName() ?></a></td>
                            <td>
                                <?php echo ($client->getStreet() ? $client->getStreet()->getName() : "")
                                    . " " . $client->getHomeNumber()
                                    . ($client->getStreet() && $client->getCity() ? ", " : "")
                                    . ($client->getCity() ? $client->getCity()->getName() : "")
                                    . ($client->getCity() && $client->getCountry() ? ", " : "")
                                    . ($client->getCountry() ? $client->getCountry()->getName() : "")
                                ?>
                            </td>
                        </tr>
                    <?php
                    endforeach;
                    ?>
                    </tbody>
                </table>
            </div>
        </div><!-- End of Card body -->
    </div>
</div>

