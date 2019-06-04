<?php
    session_start();
    if(!isset($_SESSION['nu']))
        header("location: ./index.php");
    include "db_connect.php";
    include "header.php";
?>

<html>
<head>
    <title>I Miei Annunci</title>
</head>
<body>
    <center>
    <b><p id="subtitle">I MIEI ANNUNCI</p></b>
    </center>
</body>
</html>

<?php
    $q = "SELECT ID_UTENTE,ID_IMMOBILE, immobile.INDIRIZZO ,citta.CITTA , provincia.ID_PROVINCIA , immobile.PREZZO, immobile.DESCRIZIONE, immobile.CONTRATTO, tipologia.DESCR
    FROM immobile 
    INNER JOIN citta USING (ID_CITTA) 
    INNER JOIN provincia USING (ID_PROVINCIA) 
    INNER JOIN tipologia USING (ID_TIPOLOGIA)
    WHERE ID_UTENTE = " . $_SESSION['nu'] . " LOCK IN SHARE MODE;";
    $result = $mysqli->query($q);
    if($result->num_rows > 0)
    {
        while($row = $result->fetch_assoc())
        {
            echo "<center><div id=\"block\">";

            $sql = "SELECT IMM FROM immobile WHERE ID_IMMOBILE =" . $row['ID_IMMOBILE'] . " LOCK IN SHARE MODE";
            $sth = $mysqli->query($sql);
            if($sth->num_rows > 0)
            {
                $res = $sth->fetch_array();
                echo '<img src="data:image/jpeg;base64,'.base64_encode( $res['IMM'] ).'"/>';
            }

            echo "<p><b>ID: " . $row['ID_IMMOBILE'] . "</b></p>";
            echo "<p><b>TIPOLOGIA: " . $row['DESCR'] . "</b></p>";
            echo "<p ><b>INDIRIZZO: </b>" . $row['INDIRIZZO'] . "</p>";
            echo "<p>" . $row['CITTA'] . " (" . $row['ID_PROVINCIA'] . ")" . "</p>";
            echo "<p><b>DESCRIZIONE: </b>" . $row['DESCRIZIONE'] . "</p>";
            echo "<p><b>CONTRATTO: </b>";
            if($row['CONTRATTO'] == 'a')
                echo "AFFITTO</p>";
            else
                echo "VENDITA</p>";
            echo "<p><b>PREZZO: </b>" . $row['PREZZO'] . "</p>";

            $q = "SELECT servizio.DESCRIZIONE FROM imm_serv INNER JOIN servizio USING (ID_SERVIZIO) WHERE imm_serv.ID_IMMOBILE =" . $row['ID_IMMOBILE'] . " LOCK IN SHARE MODE;";
            $res = $mysqli->query($q);
            if ($res->num_rows > 0) {
                echo "<p><b>SERVIZI: </b>";
                $i = 0;
                while($r = $res->fetch_assoc())
                {
                    if($i != 0)
                        echo ', ';
                    echo $r['DESCRIZIONE'];
                    $i++;
                }
                echo "</p>";
            }

            echo "<div style=\"right:5px;bottom:5px;\">";
            echo "<input type=\"button\" class=\"submit\" style=\"width:350px\" onclick=\"window.location.href='";
            echo $_SERVER['PHP_SELF'] . "?idi=" . $row['ID_IMMOBILE'];
            echo "'\" value=\"ELIMINA ANNUNCIO\"/>";
            echo "</div>";
            echo "</div></center><br><br>";
        }
    }
    else
        echo "<center><b><p id=\"subtitle\" style=\"color:black;\">Non hai ancora inserito alcun annuncio!<br>
        Esegui una nuova ricerca!</p></b></center>";
    
    if(isset($_GET['idi']))
    {
        $mysqli->query("START TRANSACTION");
        $q = "DELETE FROM immobile WHERE ID_IMMOBILE =" . $_GET['idi'];
        if($mysqli->query($q))
            $mysqli->query("COMMIT");
        else
            $mysqli->query("ROLLBACK");
        $curpage = $_SERVER['PHP_SELF'];
        header('location=' . $curpage);
    }

?>

