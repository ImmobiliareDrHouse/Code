<?php
    session_start();
    include "header.php";
    include "db_connect.php";
?>

<html>
<head>
    <title>Log in</title>
    <script language="Javascript">
		function ControllaForm()
		{
			var val = true;
			if (document.form.mail.value == "" )
			{
				alert("Attenzione: Hai lasciato vuoto il campo e-mail.");
				val = false;
			}

			if (document.form.psw.value == "") 
			{
				alert("Attenzione: La password inserita non e' valida.");
				val = false;
			}
			
			return val;
		}
	</script>
</head>
<body>
    <center>
        <div id="page">
            <p id="subtitle">ACCEDI</p>
            <form name="form" action="<?php echo $_SERVER["PHP_SELF"];?>" method="post" OnSubmit="return ControllaForm()">
                <b>MAIL  </b><input type="mail" name="mail"><br><br>
                <b>PASSWORD </b><input type="password" name="psw"><br><br>
                <input type="submit" class="submit" value="LOG IN" name="log"><br><br>o
            </form>

            <center><a href="./sign_up.php" id="link">Non sei ancora registrato?</a></center>
        </div>
    </center>

</body>
</html>

<?php
    if(isset($_POST['log']))
    {
        $mail = $_POST['mail'];
        $psw = $_POST['psw'];

        $query = "SELECT * FROM utente WHERE MAIL = '" . $mail . "' AND PASSWORD = '" . $psw . "' LOCK IN SHARE MODE";
        $result = $mysqli->query($query);
        if($result->num_rows > 0)
        {
            $riga = $result->fetch_assoc();
            $_SESSION['nu'] = $riga['ID_UTENTE'];
            header('Location: ./userpage.php');
        }
        else
        {
            echo "<script>alert(\"MAIL O PASSWORD NON CORRETTA!\");</script>";
        }
    }
?>
