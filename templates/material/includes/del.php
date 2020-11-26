<?php
// delete material supplier
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["delMaterialSupplier"]) ) {

    $material_id = htmlspecialchars($_GET["material_id"]);
    $client_id = htmlspecialchars($_GET["client_id_temp"]);

    $material->delMaterialSupplier($material_id, $client_id);

    die('<script>location.href = "?edit&material_id='.$material_id.'" </script>');
}

// delete material property
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["delProperty"]) ) {

    $date = date('Y-m-d h:i:s');

    $material_id = htmlspecialchars($_GET["material_id"]);
    $property_id = htmlspecialchars($_GET["property_id"]);

    // $db = new DBconnection();

    $material->delMaterialProperty($material_id, $property_id);

    die('<script>location.href = "?edit&material_id='.$material_id.'" </script>');
}
