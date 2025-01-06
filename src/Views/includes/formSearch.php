<?php

$url = explode('/', $_GET['url']);

switch ($url[0]) {

  case 'articles':
  case 'article':
    $action = '/articles/';
    break;

  case 'clients':
  case 'client':
    $action = '/clients/';
    break;

  case 'cuttings':
  case 'cutting':
    $action = '/cuttings/';
    break;

  case 'materials':
  case 'material':
    $action = '/materials/';
    break;

  case 'orders':
  case 'order':
    $action = '/orders/';
    break;

  case 'pidbs':
  case 'pidb':
    $action = '/pidbs/';
    break;

  case 'projects':
  case 'project':
    $action = '/projects/';
    break;

  default:
    $action = '/';
    break;
}
?>

<form action="<?php echo $action ?>"  method="get" class="d-none d-sm-inline-block form-inline mr-auto my-2 my-md-0 mw-100
navbar-search">
  <div class="input-group">
    <input type="text" class="form-control bg-light border-0" name="search" placeholder="Pretraga ..." aria-label="Search" aria-describedby="basic-addon2">
    <div class="input-group-append">
      <button type="submit" class="btn btn-primary">
        <i class="fas fa-sm fa-search"></i>
      </button>
    </div>
  </div>
</form>
