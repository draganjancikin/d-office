<?php
if(isset($_GET['pidb_id'])) {
    echo "ima pidb_id, biće listing transakcija za određeni dokument";
} else {
    echo "nema pidb_id, biće lista zadnjih xy transakcija";
}
