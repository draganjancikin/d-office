<?php
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../app/classes/DB.class.php';
/**
 * Cutting.class.php
 * 
 * Cutting class
 *
 * @author Dragan Jancikin <dragan.jancikin@gamil.com>
 */
class Cutting extends DB {

    protected $id;
    protected $c_id;
    protected $date;
    protected $task_id;
    protected $client_id;

    
    //metoda koja daje zadnjih $number materijala upisanih u bazu
    public function getLastCuttings($limit){
        
        $cutting = array();
        $cuttings = array();

        // izlistavanje zadnjih $limit materijala
        $result = $this->connection->query("SELECT cutting_fence.id, cutting_fence.c_id, cutting_fence.client_id, client.name "
                                         . "FROM cutting_fence "
                                         . "JOIN (client)"
                                         . "ON (cutting_fence.client_id = client.id)"
                                         . "ORDER BY cutting_fence.id DESC LIMIT $limit") or die(mysqli_error($this->connection));
        while($row = mysqli_fetch_array($result)):
            $cutting = array(
                'id' => $row['id'],
                'c_id' => $row['c_id'],
                'client_name' => $row['name']
            );
            array_push($cuttings, $cutting);
        endwhile;

        return $cuttings;
    }


    //metoda koja vraća artikle u zavisnosti od datog pojma u pretrazi
    public function search($name){

        $cutting = array();
        $cuttings = array();

        $result = $this->connection->query("SELECT id FROM cutting_fence ORDER by id desc") or die(mysqli_error($this->connection));
            $row = mysqli_fetch_array($result);
            $last_id = $row['id'];

        // izlistavanje iz baze svih artikala sa nazivom koji je sličan $name
        $result = $this->connection->query("SELECT cutting_fence.id, cutting_fence.c_id, cutting_fence.client_id, client.name "
                                         . "FROM cutting_fence "
                                         . "JOIN (client)"
                                         . "ON (cutting_fence.client_id = client.id)"
                                         . "WHERE (client.name LIKE '%$name%') "
                                         . "ORDER BY client.name ") or die(mysqli_error($this->connection));
        while($row = mysqli_fetch_array($result)):
            $cutting = array(
                'id' => $row['id'],
                'last_id' => $last_id,
                'c_id' => $row['c_id'],
                'client_name' => $row['name']
            );
            array_push($cuttings, $cutting);
        endwhile;

        return $cuttings;
    }


    //metoda koja vraća podatke o krojnoj listi od id krojne liste
    public function getCutting($cutting_id){

        $result = $this->connection->query("SELECT cutting_fence.id, cutting_fence.c_id, cutting_fence.date, cutting_fence.client_id, client.name  "
                                       . "FROM cutting_fence "
                                       . "JOIN (client) "
                                       . "ON (cutting_fence.client_id = client.id) "
                                       . "WHERE cutting_fence.id = $cutting_id ") or die(mysqli_error($this->connection));
        $row = mysqli_fetch_array($result);

        $cutting = array(
            'id' => $row['id'],
            'c_id' => $row['c_id'],
            'date' => $row['date'],
            'client_id' => $row['client_id'],
            'client_name' => $row['name']
        );

        return $cutting;
    }


    // metoda koja daje artikle u krojnoj listi
    public function getArticlesOnCutting($cutting_id){

        $article = array();
        $articles = array();

        $picket_number = 0;
        $picket_lenght = 0;
        $kap = 0;
        $total_picket_lenght = 0;
        $total_picket_number = 0;
        $total_kap = 0;

        $sir_l = 80;

        // niz treba izčitati sve artikle jedne krojne liste
        $result = $this->connection->query("SELECT cutting_fence_article.id, cutting_fence_article.cutting_fence_id, cutting_fence_article.cutting_fence_model_id, cutting_fence_article.width, cutting_fence_article.height, cutting_fence_article.mid_height, cutting_fence_article.space, cutting_fence_article.field_number, cutting_fence_model.name "
                                         . "FROM cutting_fence_article "
                                         . "JOIN (cutting_fence_model) "
                                         . "ON (cutting_fence_article.cutting_fence_model_id = cutting_fence_model.id) "
                                         . "WHERE cutting_fence_article.cutting_fence_id = $cutting_id "
                                         . "ORDER BY cutting_fence_article.id ") or die(mysqli_error($this->connection));
        while($row = mysqli_fetch_array($result)):

            $cutting_fence_id = $row['cutting_fence_id'];
            $cutting_fence_article_id = $row['id'];
            $cutting_fence_model_id = $row['cutting_fence_model_id'];

            $cutting_fence_model_name = $row['name'];
            $cutting_fence_article_width = $row['width'];
            $cutting_fence_article_height = $row['height'];
            $cutting_fence_article_mid_height = $row['mid_height'];
            $cutting_fence_article_space = $row['space'];
            $cutting_fence_article_field_number = $row['field_number'];

            // Izracunavanje broja letvica u zavisnosti od sirine polja
            $picket_number = $this->brojLetvica($cutting_fence_article_width, $cutting_fence_article_height, $cutting_fence_article_mid_height, $cutting_fence_article_space, $sir_l);

            // echo "broj tarabica=$picket_number <br />";

            $duzina_letvica_polja = 0; // reset promenljive


            // Classic ========================================
            if($cutting_fence_model_id==1){
                $raz_l = ($cutting_fence_article_width - $picket_number*$sir_l)/($picket_number+1);

                for( $i=1; $i<=ceil($picket_number/2); $i++ ){
                    $vis_l = $cutting_fence_article_height;

                    if( $i==ceil($picket_number/2) AND (ceil($picket_number/2)-($picket_number/2))>0 ){
                        $duzina_letvica_polja = $duzina_letvica_polja + $vis_l;
                    }else{
                        $duzina_letvica_polja = $duzina_letvica_polja + $vis_l*2;
                    }

                }

            }


            // Alpina ========================================
            if($cutting_fence_model_id==2){
                $raz_l = ($cutting_fence_article_width - $picket_number*$sir_l)/($picket_number+1);
                $min_max_l = $cutting_fence_article_mid_height - $cutting_fence_article_height;
                $ugao_alfa = rad2deg(atan($min_max_l/($cutting_fence_article_width/2) ));

                for( $i=1; $i<=ceil($picket_number/2); $i++ ){
                    $ras_l = $raz_l + $sir_l*($i-1) + $raz_l*($i-1);
                    $vis_raz_l = tan(deg2rad($ugao_alfa))*$ras_l;
                    $vis_l = $cutting_fence_article_height + $vis_raz_l;

                    if( $i==ceil($picket_number/2) AND (ceil($picket_number/2)-($picket_number/2))>0 ){
                        $duzina_letvica_polja = $duzina_letvica_polja + $vis_l;
                    }else{
                        $duzina_letvica_polja = $duzina_letvica_polja + $vis_l*2;
                    }

                }

            }


            // Arizona ========================================
            if($cutting_fence_model_id==3){
                $raz_l = ($cutting_fence_article_width - $picket_number*$sir_l)/($picket_number+1);
                $min_max_l = $cutting_fence_article_mid_height - $cutting_fence_article_height;
                $tetiva = SQRT((($cutting_fence_article_width-2*$raz_l)/2)*(($cutting_fence_article_width-2*$raz_l)/2) + $min_max_l*$min_max_l);
                $ugao_alfa = rad2deg(atan((2*$min_max_l)/($cutting_fence_article_width-2*$raz_l)));
                $ugao_beta = 90 - $ugao_alfa;
                $r = $tetiva / (2*cos(deg2rad($ugao_beta)));

                for( $i=1; $i<=ceil($picket_number/2); $i++ ){
                    $ras_l = $raz_l + $sir_l*($i-1) + $raz_l*($i-1);
                    $y = sqrt( $r*$r - (($cutting_fence_article_width/2 - $ras_l)*($cutting_fence_article_width/2 - $ras_l)) );
                    $vis_raz_l = $y - ($r - $min_max_l);
                    $vis_l = $cutting_fence_article_height + $vis_raz_l;

                    if( $i==ceil($picket_number/2) AND (ceil($picket_number/2)-($picket_number/2))>0 ){
                        $duzina_letvica_polja = $duzina_letvica_polja + $vis_l;
                    }else{
                        $duzina_letvica_polja = $duzina_letvica_polja + $vis_l*2;
                    }

                }

            }


            // PACIFIC ========================================
            if($cutting_fence_model_id==4){
                $raz_l = ($cutting_fence_article_width - $picket_number*$sir_l)/($picket_number+1);
                $min_max_l = $cutting_fence_article_height - $cutting_fence_article_mid_height;

                // echo "$min_max_l <br />";

                $tetiva = SQRT((($cutting_fence_article_width-2*$raz_l)/2)*(($cutting_fence_article_width-2*$raz_l)/2) + $min_max_l*$min_max_l);
                $ugao_alfa = rad2deg(atan((2*$min_max_l)/($cutting_fence_article_width-2*$raz_l)));
                $ugao_beta = 90 - $ugao_alfa;
                $r = $tetiva / (2*cos(deg2rad($ugao_beta)));

                for( $i=1; $i<=ceil($picket_number/2); $i++ ){
                    $ras_l = $raz_l + $sir_l*($i-1) + $raz_l*($i-1);
                    $y = sqrt( $r*$r - (($cutting_fence_article_width/2 - $ras_l)*($cutting_fence_article_width/2 - $ras_l)) );
                    $vis_raz_l = $y - ($r - $min_max_l);
                    $vis_l = $cutting_fence_article_height - $vis_raz_l;

                    if( $i==ceil($picket_number/2) AND (ceil($picket_number/2)-($picket_number/2))>0 ){
                        $duzina_letvica_polja = $duzina_letvica_polja + $vis_l;
                    }else{
                        $duzina_letvica_polja = $duzina_letvica_polja + $vis_l*2;
                    }

                }

            }


            // PANONKA ========================================
            if($cutting_fence_model_id==5){
                $raz_l = ($cutting_fence_article_width - $picket_number*$sir_l)/($picket_number+1);
                $min_max_l = $cutting_fence_article_mid_height - $cutting_fence_article_height;
                $omega = 360 / $cutting_fence_article_width;	//ugaona brzina
                $teta = 90;					// fazno pomeranje za 90stepeni

                for( $i=1; $i<=ceil($picket_number/2); $i++ ){
                    $ras_l = $raz_l + $sir_l*($i-1) + $raz_l*($i-1);
                    $y = sin(deg2rad($omega*$ras_l - $teta));
                    $vis_l = $cutting_fence_article_height + ($min_max_l / 2) + ($y*$min_max_l)/2;

                    if( $i==ceil($picket_number/2) AND (ceil($picket_number/2)-($picket_number/2))>0 ){
                        $duzina_letvica_polja = $duzina_letvica_polja + $vis_l;
                    }else{
                        $duzina_letvica_polja = $duzina_letvica_polja + $vis_l*2;
                    }

                }

            }


            // $total_picket_lenght = $total_picket_lenght + $duzina_letvica_polja*$cutting_fence_article_field_number;
            // $total_kap = $total_kap + $picket_number*$cutting_fence_article_field_number;

            $temp_picket_lenght = $duzina_letvica_polja*$cutting_fence_article_field_number;
            $temp_kap = $picket_number*$cutting_fence_article_field_number; 

            // echo "$duzina_letvica_polja-$cutting_fence_article_field_number <br />";

            $article = array(
                'cutting_fence_article_id' => $cutting_fence_article_id,
                'cutting_fence_id' => $cutting_fence_id,
                'cutting_fence_model_id' => $cutting_fence_model_id,
                'cutting_fence_model_name' => $cutting_fence_model_name,
                'cutting_fence_model_width' => $cutting_fence_article_width,
                'cutting_fence_article_height' => $cutting_fence_article_height,
                'cutting_fence_article_mid_height' => $cutting_fence_article_mid_height,
                'cutting_fence_article_space' => $cutting_fence_article_space,
                'cutting_fence_article_field_number' => $cutting_fence_article_field_number,
                // 'total_picket_lenght' => $total_picket_lenght,
                // 'total_kap' => $total_kap
                'temp_picket_lenght' => $temp_picket_lenght,
                'temp_kap' => $temp_kap

            );

        array_push($articles, $article);

        endwhile;

        return $articles;
    }


    public function brojLetvica($sir_p, $vis_p, $max_vis_p, $zelj_raz_l, $sir_l){
        $cont_br_letvica = ($sir_p - $zelj_raz_l) / ($sir_l + $zelj_raz_l);
        $zaok_cont_br_letvica = ceil(($sir_p - $zelj_raz_l) / ($sir_l + $zelj_raz_l));
        $razlika = $cont_br_letvica-($zaok_cont_br_letvica-1);

        if($razlika < 0.5){
            $br_letvica = ceil(($sir_p - $zelj_raz_l) / ($sir_l + $zelj_raz_l))-1;
            // $this->br_letvica = ceil(($sir_p - $zelj_raz_l) / ($sir_l + $zelj_raz_l))-1;
             // echo 'broj letvica: '.$br_letvica.'<br />';
        }

        if($razlika >= 0.5){
            $br_letvica = ceil(($sir_p - $zelj_raz_l) / ($sir_l + $zelj_raz_l));
            // $this->br_letvica = ceil(($sir_p - $zelj_raz_l) / ($sir_l + $zelj_raz_l));
            // echo 'broj letvica: '.$br_letvica.'<br />';
        }

        // $this->brojLetvica = $br_letvica;
        // return $this->type;
        return $br_letvica;
    }


    // metoda koja definiše i dodeljuje vrednost y_id
    public function setCid(){

        // čitamo iz baze, iz tabele pidb sve zapise 
        $result = $this->connection->query("SELECT * FROM cutting_fence ORDER BY id DESC") or die(mysqli_error($this->connection));

        // brojimo koliko ima zapisa
        $num = mysqli_num_rows($result); // broj kolona u tabeli $table

        $row = mysqli_fetch_array($result);
        $last_id = $row['id'];
        $year_last = date('Y', strtotime($row['date']));

        $row = mysqli_fetch_array($result);
        $year_before_last = date('Y', strtotime($row['date']));

        $c_id_before_last = $row['c_id'];

        if($num ==0){  // prvi slučaj kada je tabela $table prazna

            return die("Tabela pidb je prazna!");

        }elseif($num ==1){  // drugi slučaj - kada postoji jedan unos u tabeli $table

            $c_id = 1; // pošto postoji samo jedan unos u tabelu $table $b_on dobija vrednost '1'

        }else{  // svi ostali slučajevi kada ima više od jednog unosa u tabeli $table

            if($year_last < $year_before_last){

                return die("Godina zadnjeg unosa je manja od godine predzadnjeg unosa! Verovarno datum nije podešen");

            }elseif($year_last == $year_before_last){ //nema promene godine

                $c_id = $c_id_before_last + 1;

            }else{  // došlo je do promene godine

                $c_id = 1;

            }

        }

        $this->connection->query("UPDATE cutting_fence SET c_id = '$c_id' WHERE id = '$last_id' ") or die(mysqli_error($this->connection));

    }


    // metoda koja daje sve modele
    public function getFenceModels (){
        return $this->get("SELECT * FROM cutting_fence_model");
    }
    
}
