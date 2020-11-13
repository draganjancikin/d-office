<?php
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../app/classes/DB.class.php';
/**
 * Pidb.class.php
 * 
 * Description of Pidb class
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class Pidb extends DB {

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


    //metoda koja vraća dokumente klijenata u zavisnosti od datog pojma u pretrazi
    public function search($arr){

        $tip = $arr[0];
        $name = $arr[1];
        $archived = $arr[2];

        $pidbs = array();
        $pidb = array();

        $result = $this->connection->query("SELECT id FROM pidb ORDER by id desc") or die(mysqli_error($this->connection));
        $row = mysqli_fetch_array($result);

        // izlistavanje iz baze predračuna, računa, otpremnica i povratnica klijenata sa nazivom koji je sličan $name
        $result = $this->connection->query("SELECT pidb.id, pidb.tip_id, pidb.y_id, pidb.date, pidb.client_id, pidb.title, pidb.archived, client.name "
                                        . "FROM pidb JOIN (client)"
                                        . "ON (pidb.client_id = client.id)"
                                        . "WHERE ( (client.name LIKE '%$name%' OR client.name_note LIKE '%$name%' OR pidb.y_id LIKE '%$name%') AND pidb.tip_id = $tip AND pidb.archived = $archived )"
                                        . "ORDER BY client.name, pidb.date ") or die(mysqli_error($this->connection));
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

        return $pidbs;
    }

    /**
     * Method that return last ID in table "pidb"
     * 
     * @return integer
     * 
     * @author Dragan Jancikin <dragan.jancikin@gmail.com>
     */
    public function getlastIdPidb(){
        return $this->getLastId("pidb");
    }

   
    //metoda koja vraća podatke o dokumentu u zaisnosti od id dokumenta
    public function getPidb($pidb_id){

        $result = $this->connection->query("SELECT pidb.id, pidb.tip_id, pidb.y_id, pidb.date, pidb.client_id, pidb.title, pidb.archived, pidb.note, client.name "
                                     . "FROM pidb "
                                     . "JOIN client "
                                     . "ON (pidb.client_id = client.id) "
                                     . "WHERE pidb.id = $pidb_id ") or die(mysqli_error($this->connection));
        $row = mysqli_fetch_array($result);
            $pidb = array(
                'id' => $row['id'],
                'y_id' => $row['y_id'],
                'tip_id' => $row['tip_id'],
                'date' => $row['date'],
                'client_id' => $row['client_id'],
                'title' => $row['title'],
                'archived' => $row['archived'],
                'note' => $row['note'],
                'client_name' => $row['name']
            );

        return $pidb;
    }


    // method that give all documents by type_id
    public function getPidbs($type_id){

        $pidbs = array();
        $pidb = array();

        $result = $this->connection->query("SELECT pidb.id, pidb.y_id, pidb.date, client.name "
                                         . "FROM pidb "
                                         . "JOIN client "
                                         . "ON (pidb.client_id = client.id)"
                                         . "WHERE pidb.tip_id = $type_id ") or die(mysqli_error($this->connection));
        while($row = mysqli_fetch_array($result)):
            $pidb = array(
                'id' => $row['id'],
                'y_id' => $row['y_id'],
                'date' => $row['date'],
                'client_name' => $row['name']
            );
            array_push($pidbs, $pidb);
        endwhile;

        return $pidbs;
    }


    //metoda koja vraća sve dokumente (račune) sa $limit
    public function getLastDocuments($limit){

        $documents = array();
        $pidbs = array();
        $pidb = array();

        // izlistavanje iz baze predračuna, računa, otpremnica i povratnica klijenata sa nazivom koji je sličan $name
        for($i=1; $i<=4; $i++):
            $result = $this->connection->query("SELECT pidb.id, pidb.tip_id, pidb.y_id, pidb.date, pidb.client_id, pidb.title, pidb.archived, client.name "
                                             . "FROM pidb JOIN (client)"
                                             . "ON (pidb.client_id = client.id)"
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
            // $tax_base = ($quantity * $price * $this->kurs) - ($quantity * $price * $this->kurs) * ($discounts/100);
            $tax_base = ($quantity * $price) - ($quantity * $price) * ($discounts/100);
            $tax = $row['tax'];
            $tax_amount = $tax_base * ($tax/100);
            $sub_total = $tax_base + $tax_amount;

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
                'tax_base' => $tax_base,
                'tax' => $tax,
                'tax_amount' => $tax_amount,
                'sub_total' => $sub_total
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


    // metoda koja daje tip dokumenta
    public function getTipid($pidb_id){

        // čitamo iz baze iz tabele pidb
        $result = $this->connection->query("SELECT tip_id FROM pidb WHERE id = $pidb_id ") or die(mysqli_error($this->connection));
            $row = mysqli_fetch_array($result);

        return $row['tip_id'];
    }


    public function getAvans($pidb_id){
        $result = $this->get("SELECT * FROM payment WHERE pidb_id = '$pidb_id' AND payment_type_id = 1 ");
        return $this->sumAllValuesByKey($result, "amount");
    }


    public function sumAllValuesByKey($Arrays, $key) {
        $sumValues = 0;
        foreach ($Arrays as $subArray) {
            $sumValues += $subArray[$key];
        }
        return $sumValues;
    }

    // metoda koja daje sva potraživana<->uplate vezane za odreženi dokument $pidb_id
    public function getPayments($pidb_id){

        $payments = array();
        $payment = array();

        $result = $this->connection->query("SELECT * FROM payment WHERE pidb_id = '$pidb_id' ") or die(mysqli_error($this->connection));

        while($row = mysqli_fetch_array($result)):
            $payment = array(
                'date' => $row['date'],
                'dug_potr_id' => $row['dug_potr_id'],
                'amount' => $row['amount'],
                'note' => $row['note']
            );
            array_push($payments, $payment);
        endwhile;

        return $payments;
    }


    // metoda koja daje saldo
    public function getSaldo($pidb_id){

        $result_duguje = $this->connection->query("SELECT amount FROM payment WHERE (pidb_id = $pidb_id AND dug_potr_id = 0)") or die(mysqli_error($this->connection));
            $row_duguje = mysqli_fetch_array($result_duguje);
            $duguje = $row_duguje['amount'];

        $result_potrazuje = $this->connection->query("SELECT SUM(amount) FROM payment WHERE (pidb_id = $pidb_id AND dug_potr_id = 1)") or die(mysqli_error($this->connection));
            $row_potrazuje = mysqli_fetch_array($result_potrazuje);
            $potrazuje = $row_potrazuje['SUM(amount)'];

        $saldo = array(
            'duguje' => $duguje,
            'potrazuje' => $potrazuje
        );

        return $saldo;
    }


    public function getInvoices($client_id){

        $invoice = array();
        $invoices = array();

        $result = $this->connection->query("SELECT * FROM pidb WHERE (tip_id = 3 AND  client_id = $client_id)  ") or die(mysqli_error($this->connection));
        while($row = mysqli_fetch_array($result)):
            $invoice = array(
                'id' => $row['id'],
                'y_id' => $row['y_id'],
                'date' => $row['date'],
                'title' => $row['title']
            );
            array_push($invoices, $invoice);
        endwhile;

        return $invoices;
    }


    public function getArticlesByClient($client_id){

        $articles_id = array();

        // prvo ćemo formirati niz id-a svih artikala koje je kupovao klijent $client_id
        $result = $this->connection->query("SELECT DISTINCT pidb_article.article_id "
                                         . "FROM pidb_article "
                                         . "JOIN (pidb, article) "
                                         . "ON (pidb_article.pidb_id = pidb.id AND pidb_article.article_id = article.id) "
                                         . "WHERE pidb.client_id = '$client_id' "
                                         . "ORDER BY article.name") or die(mysqli_error($this->connection));
        while($row = mysqli_fetch_array($result)){
            $article_id = $row['article_id'];
            array_push($articles_id, $article_id);
            // promenljiva $articles_id sadrži niz svih artikala klijenta $client_id
        }

        //sada treba spakovati u niz sve artikle klijenta $client_id ===========
        $article = array();
        $articles = array();

        foreach ($articles_id as $article_id):

            $result_article = $this->connection->query("SELECT article.name, article.min_obrac_mera, unit.name as unit_name "
                                                     . "FROM article "
                                                     . "JOIN (unit) "
                                                     . "ON (article.unit_id = unit.id)"
                                                     . "WHERE article.id = '$article_id' ") or die(mysqli_error($this->connection));
            $row_article = mysqli_fetch_array($result_article);
                $article_name = $row_article['name'];
                $article_unit = $row_article['unit_name'];
                $article_min_obrac_mera = $row_article['min_obrac_mera'];

            $total_quantity = 0;
            $result_pidb_article = $this->connection->query("SELECT pidb_article.id, pidb_article.pieces "
                    . "FROM pidb_article "
                    . "JOIN (pidb, client) "
                    . "ON (pidb_article.pidb_id = pidb.id AND pidb.client_id = client.id) "
                    . "WHERE pidb_article.article_id = '$article_id' AND pidb.client_id = '$client_id' AND pidb.tip_id = '3'") or die(mysqli_error($this->connection));

            // $row_pidb_article = mysqli_fetch_array($result_pidb_article);
            while($row_pidb_article = mysqli_fetch_array($result_pidb_article)){

                $pidb_article_id = $row_pidb_article['id'];

                // treba izčitati sve property-e artikla iz tabele pidb_article_property
                $property = "";
                $temp_quantity = 1;

                $propertys = array();

                $result_propertys = $this->connection->query("SELECT pidb_article_property.quantity "
                                                       . "FROM pidb_article_property "
                                                       . "JOIN (property)"
                                                       . "ON (pidb_article_property.property_id = property.id)"
                                                       . "WHERE pidb_article_id = $pidb_article_id" ) or die(mysqli_error($this->connection));
                while($row_property = mysqli_fetch_array($result_propertys)){

                    $property_quantity = $row_property['quantity'];

                    $temp_quantity = $temp_quantity * ( $property_quantity/100 );

                }

                if($temp_quantity < $article_min_obrac_mera) $temp_quantity = $article_min_obrac_mera;

                $pieces = $row_pidb_article['pieces'];

                $quantity = round($pieces * $temp_quantity, 2);

                $total_quantity = $total_quantity + $quantity;
            }

            $article = array(
                'id' => $article_id,
                'name' => $article_name,
                'unit_name' => $article_unit,
                'quantity' => $total_quantity
            );
            array_push($articles, $article);

        endforeach;

        return $articles;
    }


    //metoda koja daje prethodni pidb_id za određeni pidb_tip_id
    public function getPreviousPidb($pidb_id, $pidb_tip_id){

        $result = $this->connection->query("SELECT * FROM pidb "
                                         . "WHERE id <'$pidb_id' AND tip_id = $pidb_tip_id "
                                         . "ORDER BY id DESC") or die(mysqli_error($this->connection));
        $row = mysqli_fetch_array($result);

        return $row['id'];
    }


    //metoda koja daje prethodni pidb_id za određeni pidb_tip_id
    public function getNextPidb($pidb_id, $pidb_tip_id){

        $result = $this->connection->query("SELECT * FROM pidb "
                                         . "WHERE id >'$pidb_id' AND tip_id = $pidb_tip_id "
                                         . "ORDER BY id ASC") or die(mysqli_error($this->connection));
        $row = mysqli_fetch_array($result);

        return $row['id'];
    }


    // metoda koja daje sve dokumente datog projekta
    public function getPidbsByProjectId($project_id) {

        $pidbs = array();
        $pidb = array();

        $result = $this->connection->query("SELECT pidb.id, pidb.y_id, pidb.tip_id, pidb.date, client.name, pidb.title "
                                         . "FROM pidb "
                                         . "JOIN client "
                                         . "ON (pidb.client_id = client.id)"
                                         . "WHERE project_id = $project_id ") or die(mysqli_error($this->connection));
        while($row = mysqli_fetch_array($result)):
            $pidb = array(
                'id' => $row['id'],
                'y_id' => $row['y_id'],
                'tip_id' => $row['tip_id'],
                'date' => $row['date'],
                'client_name' => $row['name'],
                'title' => $row['title']
            );
            array_push($pidbs, $pidb);
        endwhile;

        return $pidbs;
    }


    // metoda koja duplicira artikal iz dokumenta
    public function duplicateArticleInPidb($pidb_article_id){

        // get article by $pidb_article_id
        $articleInPidb = $this->getArticleInPidb($pidb_article_id);

        $pidb_id = $articleInPidb['pidb_id'];
        $article_id = $articleInPidb['article_id'];
        $note = $articleInPidb['note'];
        $pieces = $articleInPidb['pieces'];
        $price = $articleInPidb['price'];
        $tax = $articleInPidb['tax'];
        $weight = $articleInPidb['weight'];

        // need: pidb_id, article_id, note, pieces, price, discount, tax, weight, propertys

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


    // za brisanje
    // funkcija koja vraća ukupan godisnji prihod
    public function gettotalIncome($y){
        /*
        $result_fakturisano = $this->connection->query("SELECT SUM(amount) FROM payment WHERE (dug_potr_id = 0 AND date BETWEEN '$y-01-01 00:00:00' AND '$y-12-31 23:59:59')") or die(mysql_error());
            $row_fakturisano = mysqli_fetch_array($result_fakturisano);
            $fakturisano = $row_fakturisano['SUM(amount)'];

        $result_naplaceno = $this->connection->query("SELECT SUM(amount) FROM payment WHERE (dug_potr_id = 1 AND date BETWEEN '$y-01-01 00:00:00' AND '$y-12-31 23:59:59')") or die(mysql_error());
            $row_naplaceno = mysqli_fetch_array($result_naplaceno);
            $naplaceno = $row_naplaceno['SUM(amount)'];

        $total_income = array(
            'fakturisano' => $fakturisano,
            'naplaceno' => $naplaceno
        );

        return $total_income;
        */
    }

}
