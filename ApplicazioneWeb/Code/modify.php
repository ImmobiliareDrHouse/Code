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
    <title>Modifica Dati</title>

    <script>
        function ControllaForm()
        {
            var val = true;
            if(document.form.PASSWORD.value != "")
            {
                if(document.form.PASSWORD1.value != document.form.PASSWORD2.value)
                {
                    val = false;
                    alert("Le password non coincidono!");
                }
            }
            else if(document.form.CITTA.value != "" && document.form.PROVINCIA.value == "")
            {
                val = false;
                alert("Inserire una provincia!");
            }
            
            return val;
        }
    </script>
</head>
<body>
    <center>
    <b><p id="subtitle">MODIFICA I TUOI DATI</p></b>
    <p id="subtitle">Compila i campi dei dati che desideri aggiornare</p>
    <br>

    <form name="form" action="<?php echo $_SERVER["PHP_SELF"];?>" method='post' OnSubmit="return ControllaForm()">
        
        <b>e-mail</b>
        <input type='text' name='MAIL' /><br><br><br>
        <b>Password Precedente</b>
        <input type='password' name='PASSWORD' /><br><br>
        <b>Nuova Password</b>
        <input type='password' name='PASSWORD1' /><br><br>
        <b>Conferma Password</b>
        <input type='password' name='PASSWORD2' /><br><br><br>
        <b>Telefono</b>
        <input type='text' name='TELEFONO' /><br><br><br>
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
        <b>CARTA DI CREDITO</b>
        <input type='text' name='cdc' /><br><br>
        <input type='submit' class="submit" value='INVIA' name="update" />
    </form>
    </center>

</body>
</html>

<?php
    if(isset($_POST['update']))
    {
        $ok = true;
        if($_POST['PASSWORD'] != "")
        {
            $query = "SELECT PASSWORD FROM utente WHERE ID_UTENTE = " . $_SESSION['nu'] . " LOCK IN SHARE MODE";
            $result = $mysqli->query($query);
            $row = $result->fetch_assoc();
            $psw = $row['PASSWORD'];

            if($psw == $_POST['PASSWORD'])
            {
                
                $query = "UPDATE utente SET PASSWORD = '" . $_POST['PASSWORD1'] . "' WHERE ID_UTENTE = " . $_SESSION['nu'];
            }
            else
            {
                $ok = false;
                echo "<script>alert(\"PASSWORD INSERITA NON CORRETTA\")</script>";
            }
        }
        else if($_POST['MAIL'] != "")
        {
            $query = "UPDATE utente SET MAIL = '" . $_POST['MAIL'] . "' WHERE ID_UTENTE = " . $_SESSION['nu'];
        }
        else if($_POST['TELEFONO'] != "")
        {
            $query = "UPDATE utente SET TELEFONO = '" . $_POST['TELEFONO'] . "' WHERE ID_UTENTE = " . $_SESSION['nu'];
        }
        else if($_POST['CITTA'] != "")
        {
            $query = "UPDATE utente SET CITTA = '" . $_POST['CITTA'] . "', PROVINCIA = '" . $_POST['PROVINCIA'] ."' WHERE ID_UTENTE = " . $_SESSION['nu'];
        }
        else if($_POST['cdc'] != "")
        {
            $query = "UPDATE utente SET CARTA_DI_CREDITO = '" . $_POST['cdc'] . "' WHERE ID_UTENTE = " . $_SESSION['nu'];
        }


        if($ok)
        {
            $mysqli->query("LOCK TABLES utente WRITE;");
            $mysqli->query("START TRANSACTION");
            if($mysqli->query($query))
            {
                $mysqli->query("COMMIT");
                echo '<script>alert("AGGIORNAMENTO COMPLETATO")</script>';
                header("Location: ./userpage.php");
            }
            else
                $mysqli->query("ROLLBACK");
            $mysqli->query("UNLOCK TABLES;");
        }
    }
?>