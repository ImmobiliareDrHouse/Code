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

    <script>
        function ControllaForm()
        {
            var val = true;
            if(document.form.contr.value == "" || document.form.tipo.value == "" || document.form.PROVINCIA.value == "" || document.form.CITTA.value == "")
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
    <b><p id="subtitle">RICERCA ANNUNCIO</p></b>
    <p id="subtitle">Trova la casa fatta apposta per te!</p>
    <br>

    <form name="form" action="./feed.php" method='post' OnSubmit="return ControllaForm()">
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
        <b>Provincia</b>
            <select id="Prov" name="PROVINCIA" >
               <?php
               $query = "SELECT ID_PROVINCIA FROM provincia LOCK IN SHARE MODE";
               $result = $mysqli->query($query);
               echo "<option value=''>-     Povincia     -</option>";
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

        <b>Prezzo Minimo</b>
        <input type="number" min="1" step="any" name='PREZZO_Mi' />
        <b>Prezzo Massimo</b>
        <input type="number" min="1" step="any" name='PREZZO_Ma' /><br><br>
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

        <input type='submit' class="submit" value='CERCA!' name="search" >
    </form>
</div></center>
</body>
</html>
