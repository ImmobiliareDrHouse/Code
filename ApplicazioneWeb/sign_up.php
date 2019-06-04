<?php

    include "header.php";
    include "db_connect.php";
?>
<html>
<head>
    <title>Registrazione</title>
    <script language="Javascript">
    function ControllaForm()
    {
        var val = true;

        if (document.form.NOME.value == "") 
        {
            alert("Attenzione: Hai lasciato vuoto il campo nome.");
            val = false;
        }
        
        if (document.form.COGNOME.value == "") 
        {
            alert("Attenzione: Hai lasciato vuoto il campo cognome.");
            val = false;
        }
        if (document.form.MAIL.value == "") 
        {
            alert("Attenzione: Hai lasciato vuoto il campo e-mail.");
            val = false;
        }
        if (document.form.PASSWORD.value == "") 
        {
            alert("Attenzione: La password inserita non e' valida.");
            val = false;
        }
        if (document.form.PASSWORD1.value == "") 
        {
            alert("Attenzione: La password di conferma inserita non e' valida.");
            val = false;
        }
        if (document.form.TELEFONO.value == "") 
        {
            alert("Attenzione: Hai lasciato vuoto il campo telefono.");
            val = false;
        }
        if (document.form.CITTA.value == "") 
        {
            alert("Attenzione: Hai lasciato vuoto il campo città.");
            val = false;
        }
        if (document.form.PROVINCIA.value == "") 
        {
            alert("Attenzione: Hai lasciato vuoto il campo provincia.");
            val = false;
        }
        if (document.form.PASSWORD.value != document.form.PASSWORD1.value) 
        {
            alert("Attenzione: Le passwod inserite non coincidono.");
            val = false;
        }
        return val;
    }
    </script>
</head>
<body>
<center><div>
    <p id="subtitle">Registrazione</p>

    <form name="form" action="<?php echo $_SERVER["PHP_SELF"];?>" method='post' OnSubmit="return ControllaForm()">
        
        <b>Nome</b>
        <input type='text' name='NOME' /><br><br>
        <b>Cognome</b>
        <input type='text' name='COGNOME' /><br><br>
        <b>e-mail</b>
        <input type='text' name='MAIL' /><br><br>
        <b>Password</b>
        <input type='password' name='PASSWORD' /><br><br>
        <b>Conferma Password</b>
        <input type='password' name='PASSWORD1' /><br><br>
        <b>Telefono</b>
        <input type='text' name='TELEFONO' /><br><br>
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
            </select><br><br>
        <b>Citta'</b>
            <select id="cit" name="CITTA">
                <option value="">- Città -</option>
            </select>
        <b>CARTA DI CREDITO</b>
        <input type='text' name='cdc' /><br><br>
        <input type='submit' class="submit" value='INVIA' name="reg" />
    </form>
</div></center>
</body>
</html>
 
<?php
if(isset($_POST['reg']))
{
    $query = "SELECT * FROM utente WHERE MAIL = '" . $_POST['MAIL'] . "' LOCK IN SHARE MODE";
    $result = $mysqli->query($query);
    if($result->num_rows == 0)
    {
        $sql = "INSERT INTO
                    utente (NOME,COGNOME,MAIL,PASSWORD,TELEFONO,CITTA,PROVINCIA,CARTA_DI_CREDITO)
                VALUES
                    (?, ?, ?, ?, ?, ?, ?, ?)";
        $mysqli->query("LOCK TABLES utente WRITE;");
        $mysqli->query("START TRANSACTION");
        if($stmt = $mysqli->prepare($sql) )
        {
            $stmt->bind_param(
                "ssssssss",
                $_POST['NOME'],
                $_POST['COGNOME'],
                $_POST['MAIL'],
                $_POST['PASSWORD'],
                $_POST['TELEFONO'],
                $_POST['CITTA'],
                $_POST['PROVINCIA'],
                $_POST['cdc']
            );
            if($stmt->execute())
            {
                echo '<script>alert("UTENTE INSERITO CON SUCCESSO")</script>';
                $stmt->close();
                $mysqli->query("COMMIT");
                header('Location: ./index.php');
            }
            else
            {
                $mysqli->query("ROLLBACK");
                echo '<script>alert("UTENTE NON INSERITO")</script>';
            }
        }
        $mysqli->query("UNLOCK TABLES;");
    }
    else
        echo '<script>alert("UTENTE GIA\' ESISTENTE")</script>';
}
?>
