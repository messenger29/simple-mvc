<?php
/**** RENDER PAGE VIA TWIG ****/
class PageRender{
  protected $twig;
  protected $default_data = array();

  function __construct(){
    global $g_dev_flag;

    // default configs for twig
    $twig_env_configs = array(
      'cache' => "compilation_cache",
      'debug' => false
    );

    // checks config flag for development or production
    if($g_dev_flag){
      $twig_env_configs['cache'] = false;
      $twig_env_configs['debug'] = true;
      $twig_env_configs['optimizations'] = 0;
    }

    $loader = new Twig_Loader_Filesystem('views');
    $this->twig = new Twig_Environment($loader, $twig_env_configs);
    $this->twig->addExtension(new Twig_Extension_Debug());

    $this->default_data['base_url'] = $this->getUrl(true);
    $this->default_data['curr_url'] = $this->getUrl();

  }

  protected function getUrl($baseFlag = false) {
    $url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
    // $url .= ( $_SERVER["SERVER_PORT"] !== 80 ) ? ":".$_SERVER["SERVER_PORT"] : "";
    if($baseFlag){
      return $url;  //just the base url
    }
    $url .= $_SERVER["REQUEST_URI"];
    return $url;  //full url
  }

  /** function to push key-value content into the data variable that is
      passed onto the twig templates for use
  **/
  protected function setData($key = NULL, $val = NULL){
    if(is_null($key) || is_null($val)){
      return 0;
    }

    $this->default_data[$key] = $val;
    return 1;
  }

  protected function renderPage($template = NULL){
    //if function is called without a template
    if(is_null($template)){
      return 0;
    }

    return $this->twig->render($template,$this->default_data);
  }
}

?>