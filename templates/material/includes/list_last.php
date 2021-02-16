<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 font-weight-bold text-primary">Zadnji upisani materijali</h6>
  </div>
  <div class="card-body p-2">
    <div class="table-responsive">
      <table class="table table-bordered table-hover" id="" width="100%" cellspacing="0">
        <thead class="thead-light">
          <tr>
            <th>naziv</th>
            <th class="text-center">jed. mere</th>
            <th class="text-center">cena <br />(RSD sa PDV-om)</th>
            <th class="text-center">cena <br />(&#8364; sa PDV-om)</th>
          </tr>
        </thead>
        <tfoot class="thead-light">
          <tr>
            <th>naziv</th>
            <th class="text-center">jed. mere</th>
            <th class="text-center">cena <br />(RSD sa PDV-om)</th>
            <th class="text-center">cena <br />(&#8364; sa PDV-om)</th>
          </tr>
        </tfoot>
        <tbody>
        <?php
        // TODO Dragan
        // $materials = $material->getLastMaterials(10);
        $materials = $entityManager->getRepository('\Roloffice\Entity\Material')->getLastMaterials(10);
        foreach ($materials as $material_data):
          $material_unit = $entityManager->find('\Roloffice\Entity\Unit', $material_data->getUnit());
          ?>
          <tr>
            <td><a href="?view&material_id=<?php echo $material_data->getId() ?>"><?php echo $material_data->getName() ?></a></td>
            <td class="text-center"><?php echo $material_unit->getName() ?></td>
            <td class="text-right"><?php echo number_format( ($material_data->getPrice() * $material->getKurs() * ($material->getTax()/100 + 1) ) , 2, ",", ".") ?></td>
            <td class="text-right"><?php echo number_format( ($material_data->getPrice() * ($material->getTax()/100 + 1) ) , 1, ",", ".") ?></td>
          </tr>
          <?php
        endforeach;
        ?>
        </tbody>
      </table>
    </div>
  </div>
  <!-- End Card Body -->
</div>
