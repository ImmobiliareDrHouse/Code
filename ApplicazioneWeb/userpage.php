<?php
    session_start();
    if(!isset($_SESSION['nu']))
        header("location: ./index.php");

    include "header.php";
    include "db_connect.php";

    $query = "SELECT NOME FROM utente WHERE ID_UTENTE = " . $_SESSION['nu'] . " LOCK IN SHARE MODE";
    $result = $mysqli->query($query);
    $row = $result->fetch_assoc();
    $utente = $row['NOME'];
?>

<html>
<head>
    <title>Benvenuto!</title>
</head>
<body class="page">
    <center><b><p id="subtitle">BENVENUTO <?php echo $utente;?> !</p></b>
    <br>
    <form id="choice" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <input class ="button" type="submit" name="cerca" value="VUOI CERCARE UNA CASA?"/><br><br>
        <input class ="button" type="submit" name="ins_ann" value="VUOI INSERIRE UN ANNUNCIO?"/><br><br>
        <input class ="button" type="submit" name="vedi_ann" value="VUOI VEDERE I TUOI ANNUNCI?"/><br><br>
        <input class ="button" type="submit" name="c_dati" value="VUOI CAMBIARE I TUOI DATI PERSONALI?"/><br><br>
        <input class ="button" type="submit" name="log_out" value="LOG OUT"/><br>
    </form>
    </center>
        
</body>
</html>


<?php
    if(isset($_POST['c_dati']))
        header("Location: ./modify.php");
    else if(isset($_POST['ins_ann']))
        header("Location: ./add.php");
    else if(isset($_POST['cerca']))
        header("Location: ./search.php");
    else if(isset($_POST['log_out']))
    {
        session_abort();
        header("Location: ./index.php");
    }
    else if(isset($_POST['vedi_ann']))
        header("Location: ./my_ann.php");
?>
