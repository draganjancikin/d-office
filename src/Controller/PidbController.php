<?php

namespace Roloffice\Controller;

use Roloffice\Core\Database;

/**
 * Pidb.class.php
 * 
 * Description of Pidb class
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class PidbController extends Database {

    private $transaction_table = "payment";

    protected $id;
    protected $y_id;
    protected $tip_id;
    protected $date;
    protected $client_id;
    protected $parent_id;
    protected $project_id;
    protected $title;
    protected $archived;
    protected $note;
    protected $tax;
    protected $kurs;

    /**
     * Method that return all documnets (pidbs) where client name or client name note
     * or pidb year ID like $name
     * 
     * @param array $arr
     * @return array
     */
    public function search($arr){

        $tip = $arr[0];
        $name = $arr[1];
        $archived = $arr[2];

        $result = $this->get("SELECT pidb.id, pidb.tip_id, pidb.y_id, pidb.date, pidb.client_id, pidb.title, pidb.archived, v6_clients.name as client_name "
                            . "FROM pidb JOIN (v6_clients)"
                            . "ON (pidb.client_id = v6_clients.id)"
                            . "WHERE ( (v6_clients.name LIKE '%$name%' OR v6_clients.name_note LIKE '%$name%' OR pidb.y_id LIKE '%$name%') AND pidb.tip_id = $tip AND pidb.archived = $archived )"
                            . "ORDER BY v6_clients.name, pidb.date ");
        return $result;
    }

    /**
     * Method that return last ID in table "pidb"
     * 
     * @return integer
     */
    public function getlastIdPidb(){
        return $this->getLastId("pidb");
    }

    /**
     * Method that return Pidb data by Pidb ID
     * 
     * @param integer $pidb_id
     * 
     * @return array
     */
    /*
    public function getPidb($pidb_id){
        $result = $this->get("SELECT pidb.id, pidb.tip_id, pidb.y_id, pidb.date, pidb.client_id, pidb.title, pidb.archived, pidb.note, v6_clients.name as client_name "
                            . "FROM pidb "
                            . "JOIN v6_clients "
                            . "ON (pidb.client_id = v6_clients.id) "
                            . "WHERE pidb.id = $pidb_id ");
        if(!$result){
            die('<script>location.href = "/pidb/" </script>');
        } else {
            if($result[0]['tip_id'] == 1){
                $result[0]['type_name'] = "Predračun";
                $result[0]['type_name_abb'] = "P";
            } elseif ($result[0]['tip_id'] == 2){
                $result[0]['type_name'] = "Otpremnica";
                $result[0]['type_name_abb'] = "O";
            } elseif ($result[0]['tip_id'] == 4){
                $result[0]['type_name'] = "Povratnica";
                $result[0]['type_name_abb'] = "POV";
            }
            return $result[0];
        }
    }
    */

    /**
     * Method that return client by pidb_id
     * 
     * @param integer $pidb_id
     * @return array
     */
    public function getClientByPidbId($pidb_id) {
        $result = $this->get("SELECT v6_clients.id, v6_clients.name "
                            . "FROM v6_clients JOIN (pidb) "
                            . "ON (v6_clients.id = pidb.client_id) " 
                            . "WHERE pidb.id = '$pidb_id' ");
        return $result[0];
    }

    /**
     * Method that return last documents (pidbs)
     * 
     * @param integer $limit
     * 
     * @return array
     */
    public function getLastDocuments($limit){

        $documents = array();
        $pidbs = array();
        $pidb = array();

        // izlistavanje iz baze predračuna, računa, otpremnica i povratnica klijenata sa nazivom koji je sličan $name
        for($i=1; $i<=4; $i++):
            $result = $this->connection->query("SELECT pidb.id, pidb.tip_id, pidb.y_id, pidb.date, pidb.client_id, pidb.title, pidb.archived, v6_clients.name "
                                             . "FROM pidb JOIN (v6_clients)"
                                             . "ON (pidb.client_id = v6_clients.id)"
                                             . "WHERE pidb.tip_id = $i AND pidb.archived = 0 "
                                             . "ORDER BY pidb.id DESC LIMIT $limit ") or die(mysqli_error($this->connection));
            while($row = mysqli_fetch_array($result)):
                $pidb = array(
                    'id' => $row['id'],
                    'y_id' => $row['y_id'],
                    'tip_id' => $row['tip_id'],
                    'date' => $row['date'],
                    'client_id' => $row['client_id'],
                    'title' => $row['title'],
                    'archived' => $row['archived'],
                    'client_name' => $row['name']
                );
                array_push($pidbs, $pidb);
            endwhile;

            $documents [$i] = $pidbs;

            $pidbs = array();
            $pidb = array();

        endfor;

        return $documents;
    }

    /**
     * Method that return last transactions
     * 
     * @param int $limit
     * 
     * @return array
     */
    public function getLastTransactions($limit){
        $result = $this->get("SELECT $this->transaction_table.id, $this->transaction_table.date, $this->transaction_table.pidb_id, $this->transaction_table.amount, v6_clients.name as client_name, pidb.y_id as pidb_y_id  "
                            . "FROM $this->transaction_table "
                            . "JOIN (v6_clients, pidb)"
                            . "ON ($this->transaction_table.client_id = v6_clients.id AND $this->transaction_table.pidb_id = pidb.id )"
                            . "ORDER BY id DESC LIMIT $limit ");
        return $result;
    }

    /**
     * 
     */
    public function getDailyCashTransactions($date = "") {
        if ($date) {
            $date = $date;
        } else {
            $date = date('Y-m-d');
        }
        $result = $this->get("SELECT * "
                            ."FROM $this->transaction_table "
                            ."WHERE (created_at_date BETWEEN '$date 00:00:00' AND '$date 23:59:59') AND (type_id = 1 || type_id = 3 || type_id = 5 || type_id = 6 || type_id = 7) "
                            ."ORDER BY date ASC;");
        $i = 0;
        foreach($result as $row){
            switch ($row['type_id']) {
                case 1:
                    $type = "Avans (gotovinski)";
                    break;
                case 2:
                    $type = "Avans";
                    break;
                case 3:
                    $type = "Uplata (gotovinska)";
                    break;
                case 4:
                    $type = "Uplata";
                    break;
                case 5:
                    $type = "Početno stanje kase";
                    break;
                case 6:
                    $type = "Izlaz gotovine na kraju dana (smene)";
                    break;
                case 7:
                    $type = "Izlaz gotovine";
                    break;
                default:
                $type = "_";
                    break;
            }
            if ( $row['pidb_id'] <> 0 ) {
                $pidb_data = $this->getPidb($row['pidb_id']);
                $result[$i]['pidb_y_id'] = $pidb_data['y_id'];
                $result[$i]['client_name'] = $pidb_data['client_name'];
                $result[$i]['pidb_title'] = $pidb_data['title'];
            } else {
                $result[$i]['pidb_y_id'] = 0;
                $result[$i]['client_name'] = "";
                $result[$i]['pidb_title'] = "";
            }

            $result[$i]['type_name'] = $type;
            $i++;
        }
        return $result;
    }

    /**
     * 
     * Method that calculate daily cash saldo
     * 
     * @return double
     */
    public function getDailyCashSaldo ($date) {
        if ($date) {
            $date = $date;
        } else {
            $date = date('Y-m-d');
        }
        $result = $this->get("SELECT SUM(amount) "
                            ."FROM $this->transaction_table "
                            ."WHERE (created_at_date BETWEEN '$date 00:00:00' AND '$date 23:59:59') AND (type_id = 1 || type_id = 3 || type_id = 5 || type_id = 6)");
        return $result[0]['SUM(amount)'];
    }

    /**
     * 
     */
    public function ifExistFirstCashInput() {
        return false;
    }

    /**
     * Method that return all transactions on document (pidb)
     * 
     * @param integer $pidb_id
     * 
     * @return array
     */
    public function getTransactionsByPidbId($pidb_id){
        $result = $this->get("SELECT * FROM $this->transaction_table WHERE pidb_id = '$pidb_id' ");
        $i = 0;
        foreach($result as $row){
            switch ($row['type_id']) {
                case 1:
                    $type = "Avans (gotovinski)";
                    break;
                case 2:
                    $type = "Avans";
                    break;
                case 3:
                    $type = "Uplata (gotovinska)";
                    break;
                case 4:
                    $type = "Uplata";
                    break;
                default:
                $type = "_";
                    break;
            }
            $result[$i]['type_name'] = $type;
            $i++;
        }
        return $result;
    }

    // metoda koja definiše i dodeljuje vrednost y_id 
    public function setYid($tip_id){

        // čitamo iz baze, iz tabele pidb sve zapise 
        $result = $this->connection->query("SELECT * FROM pidb WHERE tip_id = $tip_id ORDER BY id DESC") or die(mysqli_error($this->connection));

        // brojimo koliko ima zapisa
        $num = mysqli_num_rows($result); // broj kolona u tabeli $table

        $row = mysqli_fetch_array($result);
        $last_id = $row['id'];
        $year_last = date('Y', strtotime($row['date']));

        $row = mysqli_fetch_array($result);
        $year_before_last = date('Y', strtotime($row['date']));

        $y_id_before_last = $row['y_id'];

        if($num ==0){  // prvi slučaj kada je tabela $table prazna

            return die("Tabela pidb je prazna!");

        }elseif($num ==1){  // drugi slučaj - kada postoji jedan unos u tabeli $table

            $y_id = 1; // pošto postoji samo jedan unos u tabelu $table $b_on dobija vrednost '1'

        }else{  // svi ostali slučajevi kada ima više od jednog unosa u tabeli $table

            if($year_last < $year_before_last){

                return die("Godina zadnjeg unosa je manja od godine predzadnjeg unosa! Verovarno datum nije podešen");

            }elseif($year_last == $year_before_last){ //nema promene godine

                $y_id = $y_id_before_last + 1;

            }else{  // došlo je do promene godine

                $y_id = 1;

            }

        }

        $this->connection->query("UPDATE pidb SET y_id = '$y_id' WHERE id = '$last_id' ") or die(mysqli_error($this->connection));

    }


    // metoda koja daje artikle dokumenta
    public function getArticlesOnPidb($pidb_id){

        $article = array();
        $articles = array();

        // niz $propertys bi mogli iskoristiti da se spakuju svi property-ji jednog artikla

        $result = $this->connection->query("SELECT article.name, article.unit_id, article.min_obrac_mera, pidb_article.id, pidb_article.article_id, pidb_article.note, pidb_article.pieces, pidb_article.price, pidb_article.discounts, pidb_article.tax, unit.name as unit_name "
                                         . "FROM pidb_article "
                                         . "JOIN (article, unit) "
                                         . "ON (pidb_article.article_id = article.id AND article.unit_id = unit.id)"
                                         . "WHERE pidb_article.pidb_id = $pidb_id "
                                         . "ORDER BY pidb_article.id") or die(mysqli_error($this->connection));
        while($row = mysqli_fetch_array($result)){
            $id = $row['id'];
            $article_id = $row['article_id'];
            $article_min_obrac_mera = $row['min_obrac_mera'];
            $name = $row['name'];

            // treba izčitati sve property-e artikla iz tabele pidb_article_property
            $property = "";
            $temp_quantity = 1;

            $propertys = array();

            $result_propertys = $this->connection->query("SELECT pidb_article_property.quantity, property.name "
                                                       . "FROM pidb_article_property "
                                                       . "JOIN (property)"
                                                       . "ON (pidb_article_property.property_id = property.id)"
                                                       . "WHERE pidb_article_id = $id" ) or die(mysqli_error($this->connection));
            while($row_property = mysqli_fetch_array($result_propertys)){
                $property_name = $row_property['name'];
                $property_quantity = $row_property['quantity'];

                $property = $property . $property_name . ' <input class="input-box-50" type="text" name="' .$property_name. '" value="' .$property_quantity. '" placeholder="(cm)" /> ';

                $property_niz = array(
                    'property_name' => $property_name,
                    'property_quantity' => $property_quantity
                );

                array_push($propertys, $property_niz);

                $temp_quantity = $temp_quantity * ( $property_quantity/100 );

            }

            if($temp_quantity < $article_min_obrac_mera) $temp_quantity = $article_min_obrac_mera;

            $unit_id = $row['unit_id'];
            $unit_name = $row['unit_name'];
            $note = $row['note'];
            $pieces = $row['pieces'];

            $quantity = round($pieces * $temp_quantity, 2);

            $price = $row['price'];
            $discounts = $row['discounts'];
            $tax = $row['tax'];
            
            $tax_base_per_piece = $price - round( $price * ($discounts/100), 4 );
            $tax_amount_per_piece = round( ($tax_base_per_piece * ($tax/100)), 4 );
            
            $tax_base_per_article = $tax_base_per_piece * $quantity;
            $tax_amount_per_article = $tax_amount_per_piece * $quantity;
            $sub_total_per_article = $tax_base_per_article + $tax_amount_per_article;
            
            // echo $tax_base_per_article . "+" . $tax_amount_per_article . "=" .$sub_total_per_article. "<br>";
            /*
            $tax_base = ($quantity * $price) - ($quantity * $price) * ($discounts/100);
            $tax_amount = $tax_base * ($tax/100);
            $sub_total = $tax_base + $tax_amount;
            
            echo $tax_base . "+" . $tax_amount . "=" . $sub_total. "<br>";
            */
        $article = array(
                'id' => $id,
                'article_id' => $article_id,
                'name' => $name,
                'propertys' => $propertys,
                'unit_name' => $unit_name,
                'note' => $note,
                'pieces' => $pieces,
                'quantity' => $quantity,
                'price' => $price,
                'discounts' => $discounts,
                'tax_base' => $tax_base_per_article,
                'tax' => $tax,
                'tax_amount' => $tax_amount_per_article,
                'sub_total' => $sub_total_per_article
            );
        array_push($articles, $article);
        }
        return $articles;
    }


    // metoda koja daje artikal dokumenta
    public function getArticleInPidb($pidb_article_id){

        $article = array();

        // need: pidb_id, article_id, note, pieces, price, discount, tax, weight, propertys
        $result = $this->connection->query("SELECT pidb_article.id, pidb_article.pidb_id, pidb_article.article_id, pidb_article.note, pidb_article.pieces, pidb_article.price, pidb_article.discounts, pidb_article.tax, pidb_article.weight, article.name, unit.name as unit_name "
                                         . "FROM pidb_article "
                                         . "JOIN (article, unit) "
                                         . "ON (pidb_article.article_id = article.id AND article.unit_id = unit.id)"
                                         . "WHERE pidb_article.id = $pidb_article_id ") or die(mysqli_error($this->connection));
        $row = mysqli_fetch_array($result);
            $id = $row['id'];
            $pidb_id = $row['pidb_id'];
            $article_id = $row['article_id'];
            $article_name = $row['name'];

            // treba izčitati sve property-e artikla iz tabele pidb_article_property
            $property = "";
            $temp_quantity = 1;
            $propertys = array();

            $result_propertys = $this->connection->query("SELECT pidb_article_property.quantity, property.name "
                                                       . "FROM pidb_article_property "
                                                       . "JOIN (property)"
                                                       . "ON (pidb_article_property.property_id = property.id)"
                                                       . "WHERE pidb_article_id = $id" ) or die(mysqli_error($this->connection));
            while($row_property = mysqli_fetch_array($result_propertys)){
                $property_name = $row_property['name'];
                $property_quantity = $row_property['quantity'];

                $property = $property . $property_name . ' <input class="input-box-50" type="text" name="' .$property_name. '" value="' .$property_quantity. '" placeholder="(cm)" /> ';

                $property_niz = array(
                    'property_name' => $property_name,
                    'property_quantity' => $property_quantity
                );

                array_push($propertys, $property_niz);

                $temp_quantity = $temp_quantity * ( $property_quantity/100 );
            }
            $unit_name = $row['unit_name'];           
            $note = $row['note'];
            $pieces = $row['pieces'];
            $quantity = round($pieces * $temp_quantity, 2);
            $price = $row['price'];
            $discounts = $row['discounts'];
            $tax_base = ($quantity * $price) - ($quantity * $price) * ($discounts/100);
            $tax = $row['tax'];
            $tax_amount = $tax_base * ($tax/100);
            $sub_total = $tax_base + $tax_amount;
            $weight  = $row['weight'];

        $article = array(
            'id' => $id,
            'pidb_id' => $pidb_id,
            'article_id' => $article_id,
            'article_name' => $article_name,
            'propertys' => $propertys,
            'unit_name' => $unit_name,
            'note' => $note,
            'pieces' => $pieces,
            'quantity' => $quantity,
            'price' => $price,
            'discounts' => $discounts,
            'tax_base' => $tax_base,
            'tax' => $tax,
            'tax_amount' => $tax_amount,
            'sub_total' => $sub_total,
            'weight' => $weight,
        );

        return $article;
    }


    // metoda koja briše artikal iz dokumenta
    public function delArticleFromPidb($pidb_article_id){

        $this->connection->query("DELETE FROM pidb_article WHERE id='$pidb_article_id' ") or die(mysqli_error($this->connection));

        $result_propertys = $this->connection->query("SELECT * FROM pidb_article_property WHERE pidb_article_id = $pidb_article_id ") or die(mysqli_error($this->connection));
        while($row = mysqli_fetch_array($result_propertys)):
            $id = $row['id'];

            $this->connection->query("DELETE FROM pidb_article_property WHERE id='$id' ") or die(mysqli_error($this->connection));

        endwhile;
    }

    /**
     * Method that insert transaction to table payment
     * 
     * @param string $date
     * @param integer $pidb_id
     * @param integer $client_id
     * @param integer $transaction_type_id
     * @param float $amount
     * @param string $note
     */
    public function insertTransaction($date, $pidb_id, $client_id, $type_id, $amount, $note, $created_at_date, $created_at_user_id) {
        $this->insert("INSERT INTO payment (date, pidb_id, client_id, type_id, amount, note, created_at_date, created_at_user_id) " 
        . " VALUES ('$date', '$pidb_id', '$client_id', '$type_id', '$amount', '$note', '$created_at_date', '$created_at_user_id' )");
    }

    /**
     * Total Debit Per Document
     * Ukupno zaduženje po dokumentu
     * Total Indebtedness In The Accounting Document
     * Ukupno zaduženje u računovodstvenom dokumentu
     * 
     * @param int $pidb_id
     * @return float
     */
    public function getTotalAmountsByPidbId($pidb_id) {
        $all_articles_on_pidb = $this->getArticlesOnPidb($pidb_id);
        $tax_base = $this->sumAllValuesByKey($all_articles_on_pidb, "tax_base");
        $tax_amount = $this->sumAllValuesByKey($all_articles_on_pidb, "tax_amount");
        $total = $tax_base + $tax_amount;
        return array('tax_base' => $tax_base, 'tax_amount' => $tax_amount, 'total' => $total);
    }

    /**
     * Method that return all avans payments by $pidb_id
     * 
     * @param integer $pidb_id
     * 
     * @return float
     */
    /*
     public function getAvansIncome($pidb_id){
        $result = $this->get("SELECT amount FROM payment WHERE pidb_id = '$pidb_id' AND (type_id = 1 OR type_id = 2) ");
        $avans = $this->sumAllValuesByKey($result, "amount");
        return $avans;
    }
    */

    /**
     * Method that return all income payments by $pidb_id
     * 
     * @param integer $pidb_id
     * 
     * @return float
     */
    public function getIncome($pidb_id){
        $result = $this->get("SELECT amount FROM payment WHERE pidb_id = '$pidb_id' AND (type_id = 3 OR type_id = 4) ");
        $income = $this->sumAllValuesByKey($result, "amount");
        return $income;
    }

    /**
     * Method that return parent ID
     * 
     * @param integer $id
     * 
     * @return integer 
     */
    public function getParent($id){
        $result = $this->get("SELECT parent_id FROM pidb WHERE id = '$id' ");
        return $result;
    }

    /**
     * Method
     * 
     * @param array $arrays
     * @param string $key
     * 
     * @return float
     */
    public function sumAllValuesByKey($arrays, $key) {
        $sumValues = 0;
        foreach ($arrays as $subArray) {
            $sumValues += $subArray[$key];
        }
        return $sumValues;
    }

    
    /**
     * Method that return previous ID
     * 
     * @param integer $pidb_id
     * @param integer $pidb_tip_id
     * 
     * @return integer
     */
    public function getPreviousPidb($pidb_id, $pidb_tip_id){
        $result = $this->get("SELECT id FROM pidb "
                            . "WHERE id <'$pidb_id' AND tip_id = $pidb_tip_id "
                            . "ORDER BY id DESC");
        return ( $result ? $result[0]['id'] : false );
    }


    /**
     * Method that return next ID
     * 
     * @param integer $pidb_id
     * @param integer $pidb_tip_id
     * 
     * @return integer
     */
    public function getNextPidb($pidb_id, $pidb_tip_id){
        $result = $this->get("SELECT * FROM pidb "
                            . "WHERE id >'$pidb_id' AND tip_id = $pidb_tip_id "
                            . "ORDER BY id ASC");
        return ( $result ? $result[0]['id'] : false );
    }

    /**
     * Method that return all pidb by project
     * 
     * @param integer $project_id
     * 
     * @return array
     */
    public function getPidbsByProjectId($project_id) {
        $result = $this->get("SELECT pidb.id, pidb.y_id, pidb.tip_id, pidb.date, v6_clients.name as client_name, pidb.title "
                            . "FROM pidb "
                            . "JOIN v6_clients "
                            . "ON (pidb.client_id = v6_clients.id)"
                            . "WHERE project_id = $project_id ");
        return $result;
    }

    /**
     * 
     */
    public function getAllPidbs() {
        $result = $this->get("SELECT pidb.id, pidb.y_id, pidb.tip_id, pidb.title, pidb.date, v6_clients.name as client_name "
                            ."FROM pidb "
                            ."JOIN v6_clients "
                            ."ON (pidb.client_id = v6_clients.id) "
                            ."ORDER BY date ASC");
        $i = 0;
        foreach($result as $row){
            switch ($row['tip_id']) {
                case 1:
                    $type = "P";
                    break;
                case 2:
                    $type = "O";
                    break;
                default:
                    $type = "_";
                    break;
            }
            $result[$i]['type_name'] = $type;
            $i++;
        }

        return $result;
    }

    /**
     * Method that duplicate article in document (pidb)
     * 
     * @param integer $pidb_article_id
     * 
     * @return array
     */
    /*
    public function duplicateArticleInPidb($pidb_article_id){

        $article = $this->getArticleInPidb($pidb_article_id);

        $pidb_id = $article['pidb_id'];
        $article_id = $article['article_id'];
        $note = $article['note'];
        $pieces = $article['pieces'];
        $price = $article['price'];
        $tax = $article['tax'];
        $weight = $article['weight'];

        $this->connection->query("INSERT INTO pidb_article (pidb_id, article_id, note, pieces, price, tax, weight) " 
        . " VALUES ('$pidb_id', '$article_id', '$note', '$pieces', '$price', '$tax', '$weight' )") or die(mysqli_error($this->connection));

        // treba nam i pidb_article_id (id artikla u pidb dokumentu) to je u stvari zadnji unos
        $pidb_article_id = $this->connection->insert_id;;

        //insert property-a artikla u tabelu pidb_article_property
        $propertys = $this->connection->query( "SELECT * FROM article_property WHERE article_id ='$article_id'");
        while($row_property = mysqli_fetch_array($propertys)){

            $property_id = $row_property['property_id'];
            $quantity = 0;

            $this->connection->query("INSERT INTO pidb_article_property (pidb_article_id, property_id, quantity) " 
                            . " VALUES ('$pidb_article_id', '$property_id', '$quantity' )") or die(mysqli_error($this->connection));
        }

    }
*/

    // za brisanje
    // metoda koja daje prethodni račune koji nisu plaćeni
    public function getNotPayInvoice(){
        /*
        $invoice = array();
        $invoices = array();

        $result = $this->connection->query("SELECT pidb.id, pidb.y_id, pidb.date, pidb.title, client.name "
                . "FROM pidb "
                . "JOIN (client) "
                . "ON pidb.client_id = client.id "
                . "WHERE (pidb.archived = '0' AND pidb.tip_id = '3') ") or die(mysql_error());
        $row = mysqli_fetch_array($result);
        while($row = mysqli_fetch_array($result)):
            $date = $row['date'];
            $invoice_id = $row['id'];
            $invoice_y_id = $row['y_id'];
            $title = $row['title'];
            $client_name = $row['name'];
                        
            $result_duguje = $this->connection->query("SELECT SUM(amount) FROM payment WHERE (pidb_id = $invoice_id AND dug_potr_id = 0)") or die(mysql_error());
                $row_duguje = mysqli_fetch_array($result_duguje);
                $duguje = $row_duguje['SUM(amount)'];

            $result_potrazuje = $this->connection->query("SELECT SUM(amount) FROM payment WHERE (pidb_id = $invoice_id AND dug_potr_id = 1)") or die(mysql_error());
                $row_potrazuje = mysqli_fetch_array($result_potrazuje);
                $potrazuje = $row_potrazuje['SUM(amount)'];

            if($duguje-$potrazuje==0):


            else:

               $invoice = array(
                   'id' => $invoice_id,
                   'y_id' => $invoice_y_id,
                   'date' => $date,
                   'title' => $title,
                   'client_name' => $client_name
                );
            array_push($invoices, $invoice);

            endif;

        endwhile;

        return $invoices;
        */   
    }


    // za brisanje
    // funkcija koja vraća prihod po mesecima
    public function getIncomeByMonth($y){
        /*
        $total_income = array();
        $month_income = array();

        for($m = 1; $m <= 12; $m++):

            $result_fakturisano = $this->connection->query("SELECT SUM(amount) FROM payment WHERE (dug_potr_id = 0 AND date BETWEEN '$y-$m-01 00:00:00' AND '$y-$m-31 23:59:59')") or die(mysql_error());
                $row_fakturisano = mysqli_fetch_array($result_fakturisano);
                $fakturisano = $row_fakturisano['SUM(amount)'];

            $result_naplaceno = $this->connection->query("SELECT SUM(amount) FROM payment WHERE (dug_potr_id = 1 AND date BETWEEN '$y-$m-01 00:00:00' AND '$y-$m-31 23:59:59')") or die(mysql_error());
                $row_naplaceno = mysqli_fetch_array($result_naplaceno);
                $naplaceno = $row_naplaceno['SUM(amount)'];

            $month_income = array(
                'fakturisano' => $fakturisano,
                'naplaceno' => $naplaceno
            );

            array_push($total_income, $month_income);

        endfor;

        return $total_income;
        */
    }


    // za brisanje
    // funkcija koja vraća arikle po mesecima
    public function getArticleInMonth($y, $m){
        /*
        // definišemo niz artikala koji će biti u preglednoj tabeli
        $array_articles = array(6=>0, 7=>0, 74=>0, 9=>0, 13=>0, 27=>0, 15=>0, 30=>0); 
            // 6-> pvc letvica 80x20mm, 7->kapa za letvicu 80x20mm
            //30-> pvc stub za vinograd 2,5m

        $articles_on_month = array();

        // izlistavamo redom artikle iz niza $array_articles
        foreach ($array_articles as $key_article_id => $array_article):

            $article_on_month = array();
            $month_quantity = 0;
            $result = $this->connection->query("SELECT article.unit_id, article.min_obrac_mera, pidb_article.id, pidb_article.article_id, pidb_article.pieces "
                                         . "FROM pidb_article "
                                         . "JOIN (article, unit, pidb) "
                                         . "ON (pidb_article.article_id = article.id AND article.unit_id = unit.id AND pidb_article.pidb_id = pidb.id)"
                                         . "WHERE pidb.date BETWEEN '$y-$m-01 00:00:00' AND '$y-$m-31 23:59:59' AND pidb.tip_id = 3 AND pidb_article.article_id = $key_article_id "
                                         . "") or die(mysql_error());

            while($row = mysqli_fetch_array($result)){
                $id = $row['id'];
                $article_id = $row['article_id'];

                $article_min_obrac_mera = $row['min_obrac_mera'];

                // treba izčitati sve property-e artikla iz tabele pidb_article_property
                $property = "";
                $temp_quantity = 1;

                $propertys = array();

                $result_propertys = $this->connection->query("SELECT pidb_article_property.quantity, property.name "
                                                           . "FROM pidb_article_property "
                                                           . "JOIN (property)"
                                                           . "ON (pidb_article_property.property_id = property.id)"
                                                           . "WHERE pidb_article_id = $id" ) or die(mysql_error());
                while($row_property = mysqli_fetch_array($result_propertys)){
                    $property_name = $row_property['name'];
                    $property_quantity = $row_property['quantity'];

                    $property = $property . $property_name . ' <input class="input-box-50" type="text" name="' .$property_name. '" value="' .$property_quantity. '" placeholder="(cm)" /> ';

                    $property_niz = array(
                        'property_name' => $property_name,
                        'property_quantity' => $property_quantity
                    );

                    array_push($propertys, $property_niz);

                    $temp_quantity = $temp_quantity * ( $property_quantity/100 );

                }

                if($temp_quantity < $article_min_obrac_mera) $temp_quantity = $article_min_obrac_mera;

                $pieces = $row['pieces'];

                $quantity = round($pieces * $temp_quantity, 2);

                $month_quantity = $month_quantity + $quantity;

            }

            array_push($articles_on_month, $month_quantity);

        endforeach;

        return $articles_on_month;
        */
    }

}
