<?php

include '../src/oEmbed.php';

        if ( isset($_REQUEST['url']) ){
            $OE = new PHPOEmbed(null, null);
            $data = json_decode($OE->parse($_REQUEST['url']), true);
            
            print_r($data);
            
            //$data2 = json_decode($OE->parse('youtube.com'), true);
            
//            function cool( $data ){
//                if ( isset($data['title']) ){
//                    echo '<h1>' . $data['title'] . '</h1>';
//                }
//
//                if ( isset($data['url']) ){
//                    echo '<p><a href="' . $data['url'] . '" terget="_blank">' . $data['url'] . '</a> ' . ( isset($data['author_name']) ? ' | ' . $data['author_name'] : '' ) . '</p>';
//                }
//
//                if ( isset($data['description']) ){
//                    echo '<p>' . $data['description'] . '</p>';
//                }
//
//                foreach ( $data['photos'] as $img ){
//                    echo '<img src="' . $img . '">';
//                }
//            }
//            
//            cool($data);
        }
