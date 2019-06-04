<?php
    session_start();
    if(!isset($_SESSION['nu']))
        header("location: ./index.php");
    include "db_connect.php";
    include "header.php";
?>

<html>
<head>
    <title>Ricerca Annuncio</title>
</head>
<body>
    <center>
    <b><p id="subtitle">FEED</p></b>
    </center>
</body>
</html>

<?php
    if(isset($_POST['search']))
    {
        $opz = array();
        $contratto = $_POST['contr'];
        $prov = $_POST['PROVINCIA'];
        $CITTA = $_POST['CITTA'];
            
        if($_POST['tipo'] != "QUALSIASI")
        {
            array_push($opz,"tipologia.DESCR");
            $DESCR = $_POST['tipo'];
        }
            
        if($_POST['PREZZO_Mi'] != 0)
        {
            array_push($opz,"immobile.PREZZO");
            $pr_mi = $_POST['PREZZO_Mi'];
        }
           
        if($_POST['PREZZO_Ma'] != 0)
        {
            if(!in_array("immobile.PREZZO",$opz))
                array_push($opz,"immobile.PREZZO");
            $pr_ma = $_POST['PREZZO_Ma'];
        }

        echo "<center><p id=\"subtitle\">Case conformi alle Tue richieste</p></center>";

        $query = "SELECT ID_IMMOBILE
        FROM immobile INNER JOIN
        citta USING (ID_CITTA) 
        INNER JOIN provincia USING (ID_PROVINCIA) ";

        if($_POST['tipo'] != "QUALSIASI")
            $query .= "INNER JOIN tipologia USING (ID_TIPOLOGIA) ";
        
        $query .= "WHERE immobile.CONTRATTO = '$contratto' AND 
            CITTA = '" . $CITTA . "' AND provincia.ID_PROVINCIA = '$prov' ";

        foreach($opz as $opz)
        {
            $f = explode('.',$opz);
            if($f[1] == "PREZZO")
            {
                if($_POST['PREZZO_Mi'] != 0 && $_POST['PREZZO_Ma'] == 0)
                    $query .= "AND $opz >= " . $pr_mi . " ";
                else if($_POST['PREZZO_Mi'] == 0 && $_POST['PREZZO_Ma'] != 0)
                    $query .= "AND $opz <= " . $pr_ma . " ";
                else if($_POST['PREZZO_Mi'] != 0 && $_POST['PREZZO_Ma'] != 0)
                    $query .= "AND $opz BETWEEN " . $pr_mi . " AND " . $pr_ma . " ";
            }
            else 
                $query .= "AND $opz = '${$f[1]}' ";
        }
        $query .= " AND ID_UTENTE <>" . $_SESSION['nu'] . " LOCK IN SHARE MODE";

        $opz = array();
        $id = array();
        $id_ok = array();
        $result = $mysqli->query($query);
 		
        if($result->num_rows > 0)
        {
            while($row = $result->fetch_assoc())
            {
                array_push($id,$row['ID_IMMOBILE']);
            }
           
            $ser = isset( $_POST['ser']) ? $_POST['ser'] : array();
		
            if(count($ser) > 0)
            {
                foreach($id as $im)
                {
                    //ricerca immobili completamente conformi a richieste
                    $i = 0;
                    $q = "SELECT * FROM imm_serv WHERE ID_IMMOBILE = " . $im . " AND ID_SERVIZIO = ";
                    $ok = true;
                    while($i < count($ser) && $ok)
                    {
                        $q2 = $q . $ser[$i] . " LOCK IN SHARE MODE";
                        $r = $mysqli->query($q2);
                        $ok = $r->num_rows > 0 ? true : false;
                        $i++;
                    }
                    
                    if($ok)
                        $id_ok = array_merge($id_ok,$id);
                    else
                    {
                        //ricerca immobili parzialmente conformi a richieste
                        $j = 0;
                        $q = "SELECT * FROM imm_serv WHERE ID_IMMOBILE = " . $im . " AND (";
                        while($j < count($ser))
                        {
                            $q .= "ID_SERVIZIO = $ser[$i] ";
                            if($i != count($ser)-1)
                                $q .= "OR ";
                            else 
                                $q .= ") LOCK IN SHARE MODE;";
                            $j++;
                        }
                        
                        $r = $mysqli->query($q2);
                        $ok = $r->num_rows > 0 ? true : false;
                        if($ok)
                          $id_ok = array_merge($id_ok,$id);
                    }
                }     
            }
            else
                $id_ok = array_merge($id_ok,$id);

            if(count($id_ok) == 0)
            {
                echo "<center><b><p id=\"subtitle\" style=\"color:black;\">Ci dispiace, non abbiamo trovato alcuna casa che rispecchia<br>
                tutte le tue richieste<br>
                Esegui una nuova ricerca!</p></b></center>";
            }
            else
                foreach($id_ok as $im)
                {
                    $q = "SELECT ID_UTENTE, immobile.INDIRIZZO ,citta.CITTA , provincia.ID_PROVINCIA , immobile.PREZZO, immobile.DESCRIZIONE, immobile.CONTRATTO, tipologia.DESCR
                    FROM immobile 
                    INNER JOIN citta USING (ID_CITTA) 
                    INNER JOIN provincia USING (ID_PROVINCIA) 
                    INNER JOIN tipologia USING (ID_TIPOLOGIA)
                    WHERE ID_IMMOBILE = " . $im . " LOCK IN SHARE MODE;";
                    
                    $result = $mysqli->query($q);
                    $row = $result->fetch_assoc();

                    echo "<center><div id=\"block\">";

                    $sql = "SELECT IMM FROM immobile WHERE ID_IMMOBILE =" . $im . " LOCK IN SHARE MODE";
                    $sth = $mysqli->query($sql);

                    if($sth->num_rows > 0)
                    {
                        $result = $sth->fetch_array();
                        echo '<img src="data:image/jpeg;base64,'.base64_encode( $result['IMM'] ).'"/>';
                    }
                        
                    echo "<p><b>TIPOLOGIA: " . $row['DESCR'] . "</b></p>";
                    echo "<p ><b>INDIRIZZO: </b>" . $row['INDIRIZZO'] . "</p>";
                    echo "<p>" . $row['CITTA'] . " (" . $row['ID_PROVINCIA'] . ")" . "</p>";
                    echo "<p><b>DESCRIZIONE: </b>" . $row['DESCRIZIONE'] . "</p>";
                    echo "<p><b>CONTRATTO: </b>";
                    if($row['CONTRATTO'] == 'a')
                        echo "AFFITTO</p>";
                    else
                        echo "VENDITA</p>";
                    echo "<p><b>PREZZO: </b>" . $row['PREZZO'] . " €</p>";

                    $q = "SELECT servizio.DESCRIZIONE FROM imm_serv INNER JOIN servizio USING (ID_SERVIZIO) WHERE imm_serv.ID_IMMOBILE = $im LOCK IN SHARE MODE;";
                    $result = $mysqli->query($q);
                    if ($result->num_rows > 0) {
                        echo "<p><b>SERVIZI: </b>";
                        $i = 0;
                        while($r = $result->fetch_assoc())
                        {
                            if($i != 0)
                                echo ', ';
                            echo $r['DESCRIZIONE'];
                            $i++;
                        }
                        echo "</p>";
                    }

                    echo "<div style=\"right:5px;bottom:5px;\">";
                    echo "<input type=\"button\" class=\"submit\" style=\"width:350px\" onclick=\"window.location.href='user_link.php?idu=" . $row['ID_UTENTE'] . "&idi=" . $im . "'\" value=\"CONTATTA IL VENDITORE\"/>";
                    echo "</div>";
                    echo "</div></center><br><br>";
                }
        }
        else
        {
            echo "<center><b><p id=\"subtitle\" style=\"color:black;\">Ci dispiace, non abbiamo trovato alcuna casa che rispecchia<br>
            tutte le tue richieste<br>
            Esegui una nuova ricerca!</p></b></center>";
        }
        
        $id = array();
        $id_ok = array();
    }

?>

