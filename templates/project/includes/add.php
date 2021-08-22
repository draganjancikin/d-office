<?php

// dodavanje fajla
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET['addFile']) ) {

    // echo 'upload u toku ...<br />';
    $project_id = htmlspecialchars($_GET['project_id']);
    
    // echo 'projekat' .$project_id;
    
    if ($_FILES["file"]["error"] > 0){
        
        if ($_FILES["file"]["error"]==4){ 
            echo"Molimo izaberite fajl";
        }elseif($_FILES["file"]["error"]==1){
            echo"Fajl koji ste izabrali je prevelik!";
        }else{
            echo "Greška: " . $_FILES["file"]["error"] . "<br>";
        }
        
    }else{
        
        // echo "Upload: " . $_FILES["file"]["name"] . "<br>";
        
        // $target_file = $target_dir . preg_replace("/[^a-z0-9\_\-\.]/i", '', $_FILES['file']["name"]);
        
        // echo "Type: " . $_FILES["file"]["type"] . "<br>";
        // echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
        // echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";
        
        // provera dali postoji folder određenog naloga, ako ne postoji pravljenje foldera
        if (!is_dir('upload/project_id_'.$project_id)) {
            mkdir('upload/project_id_'.$project_id);
        }
        
        $path = 'upload/project_id_'.$project_id.'/';
        
        // exit();
        // echo 'putanja do fajla je: '.$path.'<br/>';
        if (file_exists($path . $_FILES["file"]["name"])){
            echo $_FILES["file"]["name"] . " already exists. ";
        }else{
            // echo $_FILES["file"]["tmp_name"];
            if( move_uploaded_file($_FILES["file"]["tmp_name"], $path . $_FILES["file"]["name"]) ) {
                echo "jeah";
            } else {
                echo "no";
            }

            // echo "Sačuvano u: " . $path . $_FILES["file"]["name"];
        }
        // exit();    

    die('<script>location.href = "?view&project_id=' .$project_id. '" </script>');
}

}
