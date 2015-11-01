<?php
class PHPOEmbed{

  private $url;
  private static $context;
  private static $timeout = 20;
  private static $providers = array();
  private static $limitDesc = 250;
  
  /**
   * 
   * @param string $url
   */
  private function addUrl( $url ){
      
      $s = strpos($url, 'http');      
      if ( $s !== 0 ){
          $url = 'http://' . $url;
      }      
      $this->url = $url;
  }
  /**
   * 
   * @param string $url 
   * @param string $k 
   * @return mixed
   */
  private function getEmbed( $url, $k ){
      
      $cApi = preg_replace('/:url/i', $url, self::$providers[$k]['api']);            
      return $this->getUrlContent($cApi);
  }
  
  /**
   * get content from an url
   * @param string $url
   * @return string
   */
  private function getUrlContent( $url ){
      
      return @file_get_contents($url, false, self::$context);
      
  }
  
  /**
   * Check if it matches any provider given
   * @return boolean
   */
  private function checkForMatchedPattern(){
      
    foreach ( self::$providers as $key => $provider ){

        foreach ( $provider['pattern'] as $p ){
            if (preg_match($p, $this->url) ){                
                return $key;
            }
        }
    }

    return false;
  }
 
  /**
   * get the type of an url
   * @param string $url
   * @return string
   */
  private function getUrlType( $url ){
      $uInfo = parse_url($url);
      
      if ( empty( $uInfo['path'] ) ){
          return 'link';
      }
      
      $e = explode('.', $uInfo['path']);
      
      switch (end($e)){
          case 'gif':
          case 'jpeg':
          case 'jpg':
          case 'png':
              return 'photo';
          default: 
              return 'link';
      }
      
  }
  
  /**
   * use this method if there is matched pattern found
   * 
   * @param string $url
   * @return string return null on not matched
   */
  private function fetchDefault( $url ){
       
      $content = $this->getUrlContent($url);            
      $type = $this->getUrlType( $url );      
      
      if ( $type == 'photo' ){
          return json_encode(array(
              'type' => 'photo',
              'url' => $url,
              'href' => $url,
              'version' => '1.0'
          ));
      }
      
      if ( $type == 'link' ){
          return $this->filterContent( $content, $url );
      }
      
      return null;
  } 
  /**
   * it filters out the content for embed
   * @param string $content give the website content as string
   * @param string $url give the website url
   * @return mixed
   */
  private function filterContent( $content, $url ){      
      
        //try to get the encoding
        $matches = array();        
        preg_match('/<\s*meta\s*[^\>]*?http-equiv=[\'"]content-type[\'"][^\>]*?\s*>/i',$content,$matches);
        $meta = empty($matches[0]) ? null : $matches[0];

        preg_match('/content=[\'"][^\'"]*?charset=([\w-]+)(:[^\w-][^\'"])*?[\'"]/i',$meta,$matches);
        $encoding = empty($matches[1]) ? 'UTF-8' : $matches[1];
        //end getting the encoding
        
        //try to get the website title
        preg_match('/<\s*title[^>]*>([\s\S]*?)<\s*\/\s*title\s*>/i', $content, $matches);
        $title = empty($matches[1]) ? null : mb_convert_encoding($matches[1], 'UTF-8', $encoding);        
        //end getting website title
        
        //try to get the website metha description
        $matches = array();
        $meta = "";
        preg_match('/<\s*meta\s*[^\>]*?name=[\'"]description[\'"][^\>]*?\s*>/i',$content,$matches);
        $meta = empty($matches[0]) ? null : $matches[0];
        
        $matches = array();
        preg_match('/content=[\'"]([\s\S]*?)[\'"]/i',$meta,$matches);        
        $description = empty($matches[1]) ? null : mb_convert_encoding($matches[1], 'UTF-8', $encoding);
        //end getting the description
        
        //try to get the meta author
        $matches = array();
        $meta = "";
        preg_match('/<\s*meta\s*[^\>]*?name=[\'"]author[\'"][^\>]*?\s*>/i', $content, $matches);
        $meta = empty($matches[0]) ? null : $matches[0];
        
        $matches = array();
        preg_match('/content=[\'"](.*?)[\'"]/i', $meta, $matches);
        $author = empty($matches[1]) ? null : mb_convert_encoding($matches[1], 'UTF-8', $encoding);
        //end getting website author
        
        //try to get all the images from <img> tag
        $imatches = array();
        preg_match_all('/<\s*img\s*.*?src=[\'"](.+?)[\'"][^>]*>/i',$content, $imatches);
        //end getting all the images
        
        //if the description is empty run the code bellow
        if ( empty($description)  ){
            //try to grab one of <p> tags
            $matches = array();            
            preg_match('/<\s*p[^>]*>([\w\W]*)<\s*\/\s*p\s*>/i', $content, $matches);
            
            //if the <p> tag is found filter it
            if ( !empty($matches[1]) ){ 
                //remove any unwanted <script> tag
                $d = preg_replace('/<\s*script[^>]*>(?:[\w\W]*)<\s*\/\s*script\s*>/i', '', $matches[1]);
                //remove any unwanted <style> tag
                $d = preg_replace('/<\s*style[^>]*>(?:[\w\W]*)<\s*\/\s*style\s*>/i', '', $d);                                
                //remove any other tags
                $d = preg_replace('/<.+?>/i', ' ', $d);                
                //remove any line break ot tabs
                $d = preg_replace('/[\r\n\t]/i', '', $d);                
                //convert more than one space to one space
                $d = preg_replace('/\s+/', ' ', $d);
                //remove any unwanted commant tag for html
                $d = preg_replace('/<!--[^>]*>/i', '', $d);
                //endcode and limit the description
                $d = substr(mb_convert_encoding($d, 'UTF-8', $encoding), 0, self::$limitDesc);
                //add '...' after the description
                $d .= strlen($d) > self::$limitDesc ? '...' : '';
                //assign the new description
                $description = !empty($d) ? $d : $description;
            }
            
        }
        
        $images = array();
        
        //loop through every images it grabed
        foreach ( $imatches[1] as $img )
        {
            $urlInfo = parse_url($url);
            $imgInfo = parse_url($img);

            if ( empty($imgInfo['host']) )
            {
                $imgDir = dirname($imgInfo['path']);
                
                $urlScheme = empty($urlInfo['scheme']) ? '' : $urlInfo['scheme'] . '://';
                $urlAddr = $urlScheme . $urlInfo['host'];

                if ( strpos($imgDir, '/') === 0 )
                {
                    $img = $urlAddr . $imgInfo['path'];
                }
                elseif ( !empty($urlInfo['path']) )
                {
                    $pp = pathinfo($urlInfo['path']);
                    $urlPath = $pp['dirname'] . ( empty($pp['extension']) ? $pp['basename'] . '/' : '' );
                    $img = $urlAddr . $urlPath . $imgInfo['path'];
                }
                else
                {
                    $img = $urlAddr . '/' . $imgInfo['path'];
                }
            }
            //filter for unique images
            if ( array_search($img, $images) === FALSE ){
                $images[] = $img;
            }
        }

        $firstImg = reset($images);
        $firstImg = $firstImg ? $firstImg : null;
        
        //return data as json object
        return json_encode(array(
            'type' => 'link',
            'version' => '1.0',
            'title' => $title,
            'url' =>  $url,
            'author_name' => $author,
            'description' => $description,
            'photos' => $images
        ));
        
  }
  
  public function __construct(  ){      
      //create the context for file_get_conteints
      if ( self::$context == null ){
        self::$context = stream_context_create(array('http' => array(           
           'timeout' => self::$timeout,
           'header' => "User-Agent: Ullash Open source\r\n"
        )));
      }
  }
  /**
   * add the cus to providers 
   * 
   * @param PHPOEmbedProvider $provider
   */
  public static function addProvider( PHPOEmbedProvider $provider ){

      self::$providers[$provider->getUKey()] = array('api' => $provider->getApi() , 'pattern' => $provider->getPattern() );

  }
  /**
   * Parse and get the content you want from an url
   * 
   * @param string $url provide the url to parse
   * @return mixed 
   */
  public function parse( $url ){
      
        $this->addUrl(  $url  );
        $k = $this->checkForMatchedPattern();        
        //check the url if it mathches any pattern
        if ( $k  !== FALSE ){
            if ( $ge = $this->getEmbed($this->url, $k ) ){
                return $ge;
            }
        }        
        //if it does not matches any patter then default fetch
        return $this->fetchDefault($this->url);
  }
  
}

class PHPOEmbedProvider{

  private $api;
  private $pattern;
  private $uKey;
  
  /**
   * 
   * @param string $api provide the api 
   * @param string $pattern regex pattern for url pattern
   * @param string $key provide an unique key for provider
   */
  public function __construct( $api, $pattern, $key ){
    $this->api = $api;
    $this->pattern = (array) $pattern;
    $this->uKey = $key;
  }

  public function getApi(){
    return $this->api;
  }

  public function getPattern(){
    return $this->pattern;
  }
  
  public function getUKey(){
      return $this->uKey;
  }

}

require 'providers.php';