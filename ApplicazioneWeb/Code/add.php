<?php ob_start(); ?>
<?php
    session_start();
    if(!isset($_SESSION['nu']))
        header("location: ./index.php");
    include "db_connect.php";
    include "header.php";
?>

<html>
<head>
    <title>Inserisci Annuncio</title>

    <script>
        function ControllaForm()
        {
            var val = true;
            if(document.form.INDIRIZZO.value == "")
            {
                val = false;    
            }
            else if(document.form.PROVINCIA.value == "")
            {
                val = false;
            }
            else if(document.form.CITTA.value == "")
            {
                val = false;
            }
            else if(document.form.PREZZO.value == "")
            {
                val = false;
            }
            else if(document.form.descr.value == "")
            {
                val = false;
            }
            if(!val)
                alert("Completa form!");
            return val;
        }
    </script>

</head>
<body>
    <center>
    <b><p id="subtitle">INSERISCI ANNUNCIO</p></b>
    <p id="subtitle">Aiuta gli altri a trovare la casa perfetta per loro!</p>
    <br>

    <form name="form" action="<?php echo $_SERVER["PHP_SELF"];?>" method='post' enctype="multipart/form-data" OnSubmit="return ControllaForm()">
        <b>Contratto</b>
        <input type="radio" name="contr" value="a" checked><b style="color:black">Affitto</b> 
        <input type="radio" name="contr" value="v"><b style="color:black">Vendita</b><br><br>
        <b>Tipologia</b>
            <select name="tipo" >
               <?php
               $query = "SELECT DESCR FROM tipologia LOCK IN SHARE MODE";
               $result = $mysqli->query($query);
               echo "<option value=''>-     Tipologia     -</option>";
               while($r = $result->fetch_assoc())
               {
                   $p = $r['DESCR'];
                   echo "<option value='" . $p . "'>" . $p . "</option>";
               }
               ?>
            </select><br><br>
        <b>Indirizzo</b>
        <input type='text' name='INDIRIZZO' /><br><br>
        <b>Provincia</b>
            <select id="Prov" name="PROVINCIA" >
               <?php
               $query = "SELECT ID_PROVINCIA FROM provincia LOCK IN SHARE MODE";
               $result = $mysqli->query($query);
               echo "<option value=''>-     Provincia     -</option>";
               while($r = $result->fetch_assoc())
               {
                   $p = $r['ID_PROVINCIA'];
                   echo "<option value='" . $p . "'>" . $p . "</option>";
               }
               ?>
            </select>
        <b>Citta'</b>
            <select id="cit" name="CITTA">
            </select><br><br>
        <b>Prezzo</b>
        <input type="number" min="1" step="any" name='PREZZO' /><br><br>
        <b>Descrizione</b>
        <textarea name="descr" id="descr" cols="30" rows="10"></textarea><br><br>
        <b>Immagine</b>
        <input type="file" name="imm" /><br><br>
        <b>Servizi</b>
        <?php
            $query = "SELECT ID_SERVIZIO,DESCRIZIONE FROM servizio LOCK IN SHARE MODE";
            $result = $mysqli->query($query);
            echo "<center><div>";
            echo "<div style=\"width:400px;float:left;\">";
            $i=0;
            while($r = $result->fetch_assoc())
            {
                if($i == 7 || $i == 14)
                    echo "</div>";
                if($i == 7)
                    echo "<div style=\"width:400px;float:left;\">";
                echo "<p style=\"color:black\"><input type=\"checkbox\" name=\"ser[]\" value=\"" . $r['ID_SERVIZIO'] . "\">" . $r['DESCRIZIONE'] . "</p>";
                $i++;
            }
            echo "</div></center><br><br>";
        ?>

        <input type='submit' class="submit" value='PUBBLICA!' name="succ" >
    </form>
</div></center>
</body>
</html>
<?php
if(isset($_POST['succ']))
{
    $utente = $_SESSION['nu'];
    $ind = $_POST['INDIRIZZO'];
    $c = $_POST['CITTA'];
    $pr = $_POST['PREZZO'];
    $tipo = $_POST['tipo'];
    $des = $_POST['descr'];
    $contr = $_POST['contr'];

    $qr = "SELECT ID_CITTA FROM citta WHERE CITTA = '" . $c . "' LOCK IN SHARE MODE";
    $result = $mysqli->query($qr);
    $r = $result->fetch_assoc();
    $c = $r['ID_CITTA'];
    $qr = "SELECT ID_TIPOLOGIA FROM tipologia WHERE DESCR = '" . $tipo . "' LOCK IN SHARE MODE";
    $result = $mysqli->query($qr);
    $r = $result->fetch_assoc();
    $id_tipo = $r['ID_TIPOLOGIA'];

    if (file_exists($_FILES['imm']['tmp_name']) AND is_uploaded_file($_FILES['imm']['tmp_name']))
    {
        $nome_file_temporaneo = $_FILES['imm']['tmp_name'];
        $nome_file_vero = $_FILES['imm']['name'];

        // leggo il contenuto del file
        $dati_file = file_get_contents($nome_file_temporaneo);

        // preparo il contenuto del file per la query
        $dati_file = addslashes($dati_file);
    }
    else
        $dati_file = "";

    $mysqli->query("LOCK TABLES immobile WRITE");
    $mysqli->query("START TRANSACTION");
    $q = "INSERT INTO immobile (ID_UTENTE, INDIRIZZO, ID_CITTA, PREZZO, DESCRIZIONE, IMM, CONTRATTO, ID_TIPOLOGIA) VALUES ('" . $utente . "','" . $ind . "','" . $c . "'," . $pr . ",'" . $des . "','" . $dati_file . "','" . $contr . "'," . $id_tipo . ")";
    if($mysqli->query($q))
        $mysqli->query("COMMIT");
    else
        $mysqli->query("ROLLBACK");
    $mysqli->query("UNLOCK TABLES;");
    
    $query2 = "SELECT ID_IMMOBILE FROM immobile ORDER BY ID_IMMOBILE DESC LIMIT 1 LOCK IN SHARE MODE";
    $result = $mysqli->query($query2);
    $r = $result->fetch_assoc();
    $id = $r['ID_IMMOBILE'];

    $ser_id = isset($_POST['ser']) ? $_POST['ser'] : array();
    
    foreach($ser_id as $v)
    {
        $q = "INSERT INTO imm_serv (ID_IMMOBILE, ID_SERVIZIO) VALUES ('" . $id . "','" . $v . "')";
        $mysqli->query("LOCK TABLES imm_serv WRITE;");
        $mysqli->query("START TRANSACTION");
        if($mysqli->query($q))
            $mysqli->query("COMMIT");
        else
            $mysqli->query("ROLLBACK");
        $mysqli->query("UNLOCK TABLES;");
    }

    header("Location: ./userpage.php");
}
?>
