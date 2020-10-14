<?php

// $start može biti:
// '' - prazno polje ako neko briše datum u formi,
// '0000-00-00 00:00:00' - ako do sada nije menjan datum
// '2017-01-18 09:35:26' - neka druga vrednost ako je setovan datum početka 
//                         realizacije zadatka
//
// $end može biti:
// '' - prazno polje ako neko briše datum u formi,
// '0000-00-00 00:00:00' - ako do sada nije menjan datum
// '2017-01-18 09:35:26' - neka druga vrednost ako je setovan datum završetka 
//                         realizacije zadatka
//
//

if($start == '0000-00-00 00:00:00' AND $end == '0000-00-00 00:00:00') {
    
    // zadatak je nov i još nije setovan ni start ni end
    // $status_id = 1;
    echo 'zadatak je nov i još nije setovan ni start ni end';
    exit();
    
}elseif ($start == '' AND $end == '0000-00-00 00:00:00') {
    
    // start je postojao pa je brisan u formi a end nije setovan
    // $start='0000-00-00 00:00:00';
    // $status_id = 1;
    echo 'start je postojao pa je brisan u formi a end nije setovan';
    exit();
    
}elseif (!$start == '0000-00-00 00:00:00' AND !$start == '' AND $end =='') {
    
    // start je setovan i ne menja se a end je setovan pa se briše
    // $result_start = $connection->query("SELECT * FROM project_task WHERE id = '$task_id' ") or die(mysql_error());
    //     $row_start = mysqli_fetch_array($result_start);
    //     $start = $row_start['start'];
    echo 'start je setovan i ne menja se a end je setovan pa se briše';
    exit();
    
}


