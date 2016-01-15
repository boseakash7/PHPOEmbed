<?php

include '../src/oEmbed.php';
//echo ini_get('safe_mode');
//phpinfo();
$i =  parse_url('//asd/asd2/fgd.gif');
var_dump($i);
?>
<html>
    <head>
        <title>PHPOEmbed test <script>alert("awesome");</script>file</title>
    </head>
    <body>
        <p>  
            
        </p>
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">
            <table>
                <tr><td>Url:</td><td><input type="text" name="embed" class="embed_text_input"></td></tr>
                <tr><td></td><td><input type="submit" name="embed_submit"></td></tr>
            </table>
        </form>
        <?php
        
        if ( isset($_GET['embed_submit']) ){
            $OE = new PHPOEmbed(null, "Ullash open source\r");
            $data = json_decode($OE->parse($_GET['embed']), true);
            
            
            
            //$data2 = json_decode($OE->parse('youtube.com'), true);
            
            function cool( $data ){
                if ( isset($data['title']) ){
                    echo '<h1>' . $data['title'] . '</h1>';
                }

                if ( isset($data['url']) ){
                    echo '<p><a href="' . $data['url'] . '" terget="_blank">' . $data['url'] . '</a> ' . ( isset($data['author_name']) ? ' | ' . $data['author_name'] : '' ) . '</p>';
                }

                if ( isset($data['description']) ){
                    echo '<p>' . $data['description'] . '</p>';
                }

                foreach ( $data['photos'] as $img ){
                    echo '<img src="' . $img . '">';
                }
            }
            
            cool($data);
//            cool($data2);
        }
//        print_r(get_html_translation_table(HTML_ENTITIES, ENT_COMPAT | ENT_HTML5, 'UTF-8'));
        
        ?>
    </body>
</html>