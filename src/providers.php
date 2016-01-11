<?php
 PHPOEmbed::addProvider( new PHPOEmbedProvider('http://www.youtube.com/oembed?url=:url&format=json', array(
     '~youtube\.com/watch.+v=[\w-]+&?~',
     '~youtu.be\/[\w-]+~'
     ), 'youtube' ) );
 PHPOEmbed::addProvider( new PHPOEmbedProvider('http://www.flickr.com/services/oembed?url=:url&format=json', 
         '~flickr\.com/photos/[-.\w@]+/\d+/?~', 
         'fliker' ) );
 PHPOEmbed::addProvider( new PHPOEmbedProvider('http://www.vimeo.com/api/oembed.json?url=:url', 
         '~vimeo\.com/.+~', 
         'vimeo' ) ); 
 PHPOEmbed::addProvider( new PHPOEmbedProvider('http://www.hulu.com/api/oembed.json?url=:url', 
     '~hulu\.com/watch/.+~',
     'hulu' ) );
 PHPOEmbed::addProvider( new PHPOEmbedProvider('http://www.polleverywhere.com/services/oembed?url=:url&format=json',  array(
        '~polleverywhere\.com/polls/.+~',
        '~polleverywhere\.com/multiple_choice_polls/.+~',
        '~polleverywhere\.com/free_text_polls/.+~'
    ), 'polle' ) );

 
 