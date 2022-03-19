<?php
    include ("stringtokenizerclass.php"); 
    include ("lexicoclass.php");
    
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>ANALIZADOR LEXICO UNA- PUNO</title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>

    <div>
        <h1>ANALIZADOR LEXICO SIMPLE DE OPERACIONES </h1>
        <br />
        <?php
            $txt = '';
            $fp = fopen("fuente.dat", "r");
            while(!feof($fp)) $txt .= fgets($fp);
            fclose($fp);

            echo "<b>ARCHIVO (fuente.dat)</b>: <BR /><PRE>".$txt."</PRE>";
            $lexer = new Lexer($txt);
        ?>
    </div>

</body>
</html>