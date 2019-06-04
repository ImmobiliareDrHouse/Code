<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href="https://fonts.googleapis.com/css?family=Fredericka+the+Great" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="ajaxListener.js"></script>
</head>
<body>
    <center>
        <div id="title">
            <b>drHouse</b>
            <br>
            <b>Immobiliare</b>
        </div>
    </center>
	<div style="position:absolute;right:5px;top:5px;">
      <form action="<?php 
                        if(isset($_SESSION['nu']))
                            echo "./userpage.php";
                        else
                            echo "./index.php";
                    ?>">
      <input type="submit" value='' style="border-radius:50px;width:52px;height:50px;background : url(img/home.png);" />
      </form>
    </div>
<br>
</body>
</html>