<?php
include('db_connect.php');

if(isset($_POST["prov"]))
{
    echo '<option value="">- Città -</option>';
    $result = $mysqli->query("SELECT CITTA FROM citta WHERE ID_PROVINCIA='" . $_POST["prov"] ."' ORDER BY CITTA LOCK IN SHARE MODE;"); 
    while ($riga = $result->fetch_assoc()){ 
        $id = $riga['CITTA'];
        echo "<option value=\"" . $id . "\">" . $id . "</option>";
    }
}
?>