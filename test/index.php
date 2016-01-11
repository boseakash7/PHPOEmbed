<?php

include '../src/oEmbed.php';




?>
<html>
    <head>
        <title>PHPOEmbed test file</title>
    </head>
    <body>
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">
            <table>
                <tr><td>Url:</td><td><input type="text" name="embed" class="embed_text_input"></td></tr>
                <tr><td></td><td><input type="submit" name="embed_submit"></td></tr>
            </table>
        </form>
        <?php
        
        if ( isset($_GET['embed_submit']) ){
            $OE = new PHPOEmbed();
            $data = json_decode($OE->parse($_GET['embed']), true);
            
            print_r($data);
        }
        
        
        ?>
    </body>
</html>