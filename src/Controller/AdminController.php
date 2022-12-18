<?php

namespace Roloffice\Controller;

require_once __DIR__ . '/../../config/dbConfig.php';
/**
 * AdminController class
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class AdminController {

    public function baseBackup (){
        $dumpfile = "roloffice_" . date("Y-m-d_H-i-s") . "_" . ENV . "_" . VERSION . ".sql";

        // passthru("C:/xampp/mysql/bin/mysqldump --opt --host=DB_SERVER --user=DB_USERNAME --password=DB_PASSWORD DB_NAME > D:/Documents/BackUps/MYSQL/$dumpfile");

        $command = "C:/xampp/mysql/bin/mysqldump --opt --host=" .DB_SERVER. " --user=" .DB_USERNAME. " --password=" .DB_PASSWORD. " "  .DB_NAME. " > D:/Documents/BackUps/MYSQL/" .$dumpfile;

        try {
            exec($command);
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        echo '
            <div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-info"></i> Obaveštenje!</h4>
                Backup baze je izvšen u fajl: <br />D:/Documents/BackUps/MYSQL/' . $dumpfile . '
            </div>
            ';

        passthru("tail -1 $dumpfile");
    }

//    public function getCompanyInfo() {
//
//        $client_data = $entityManager->find('\Roloffice\Entity\Client', $client_id);
//        return [
//            'name' => 'PREDRAG GAJIĆ PR ROLOSTIL',
//
//        ];
//    }

}
