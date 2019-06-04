<?php
    session_start();
    if(!isset($_SESSION['nu']))
        header("location: ./index.php");
    include "db_connect.php";
    include "header.php";
?>

<html>
<head>
    <title>Contatta il venditore</title>
</head>
<body>
    <center><form name="form" action="<?php echo $_SERVER["PHP_SELF"] . "?idu=" . $_GET['idu'] . "&idi=" . $_GET['idi'];?>" method='post'>
        <b>mail</b><br><br>
        <textarea name="msg" id="descr" cols="40" rows="10" style="font-size:30px">
        <?php
            echo "Salve,\n";
            echo "sarei interessato/a all'immobile n. " . $_GET['idi'] . " da lei proposto su drhouseimmobiliare.\n\n Attendo un suo riscontro \n Cordialmente \n\n";
            $q = "SELECT NOME,COGNOME FROM utente WHERE ID_UTENTE =" . $_SESSION['nu'] . " LOCK IN SHARE MODE";
            $r = $mysqli->query($q);
            $el = $r->fetch_assoc();
            $nc = $el['NOME'] . " " . $el['COGNOME'];
            echo $nc;
        ?>
        </textarea><br><br>
        <input type='submit' class="submit" value='INVIA!' name="invio" >
        </form></center>
</body>
</html>

<?php
    if(isset($_POST['invio']))
    {
        $q = "SELECT MAIL FROM utente WHERE ID_UTENTE =" . $_GET['idu'] . " LOCK IN SHARE MODE";
        $r = $mysqli->query($q);
        $el = $r->fetch_assoc();
        $mail = $el['MAIL'];
        $q = "SELECT MAIL FROM utente WHERE ID_UTENTE =" . $_SESSION['nu'] . " LOCK IN SHARE MODE";
        $r = $mysqli->query($q);
        $el = $r->fetch_assoc();
        $mymail = $el['MAIL'];
        $mail_headers = "From: " .  $nc . " <" .  $mymail . ">\r\n";
		$mail_headers .= "Reply-To: " .  $mymail . "\r\n";
		$mail_headers .= "X-Mailer: PHP/" . phpversion();
        mail(
            $mail,
            'RICHIESTA IMMOBILE',
            $_POST['msg'],
            $mail_headers
        );
       header("location:./userpage.php");
    }
?>