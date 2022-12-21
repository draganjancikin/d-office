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

        $dump_dir = '';
        $dumpfile = "roloffice_" . date("Y-m-d_H-i-s") . "_" . ENV . "_" . APP_VERSION . ".sql";

        // Check OS version.
        $os = PHP_OS;
        if ($os == 'Windows') {
            // $root = "D:/Documents/BackUps/MYSQL/";
            $dump_dir = getenv('HOMEDRIVE') . getenv('HOMEPATH') . '\Downloads';
        } elseif ($os == 'Linux') {
            // @HOLMES - Need define Download folder for Linux systems.
            $dump_dir = __DIR__ . '/../home/dragan/Downloads/';
        }

        // passthru("C:/xampp/mysql/bin/mysqldump --opt --host=DB_SERVER --user=DB_USERNAME --password=DB_PASSWORD DB_NAME > D:/Documents/BackUps/MYSQL/$dumpfile");
        $command = "C:/xampp/mysql/bin/mysqldump --opt --host=" . DB_SERVER . " --user=" . DB_USERNAME . " --password=" . DB_PASSWORD . " "  . DB_NAME . " > " . $dump_dir . $dumpfile;

        try {
            exec($command);
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        echo '
            <div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-info"></i> Obaveštenje!</h4>
                Backup baze je izvšen u fajl: <br />' . $dump_dir . $dumpfile . '
            </div>
            ';

        passthru("tail -1 $dumpfile");
    }

}
