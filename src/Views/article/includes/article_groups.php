<!-- List all Article Groups -->
<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 font-weight-bold text-primary">Grupe proizvoda</h6>
  </div>
  <div class="card-body p-2">
    <div class="table-responsive">
      <table class="dataTable table table-bordered table-hover" id="" width="100%" cellspacing="0">
        <thead class="thead-light">
          <tr>
            <th>Naziv</th>
          </tr>
        </thead>
        <tfoot class="thead-light">
          <tr>
            <th>Naziv</th>
          </tr>
        </tfoot>
        <tbody>
          <?php
          foreach ($article_groups as $group_data):
            ?>
            <tr>
              <td><a href="/articles/group/<?php echo $group_data->getId() ?>"><?php
                  echo
                  $group_data->getName() ?></a></td>
            </tr>
            <?php
          endforeach;
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
