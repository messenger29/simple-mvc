<?php
require_once '../config/globals.php';

if($g_debug_flag){
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
}

date_default_timezone_set('America/Los_Angeles');
require_once '../vendor/autoload.php';

/**** RENDER PAGE VIA TWIG ****/
require_once("controllers/class.pagerender.php");

class renderHome extends PageRender{
  public function render(){
    echo $this->renderPage('home.twig');
  }
}

class render404 extends PageRender{
  public function render(){
    echo $this->renderPage('page404.twig');
  }
}

/**** ROUTING ****/
$router = new AltoRouter();

// Home
$router->map( 'GET', '/', 'renderHome#render');

// Starting matching URL
$match = $router->match();

//check matching
if($match === false){
  //header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
  $obj = new render404();
  call_user_func_array(array($obj,'render'), array());
}
elseif($match['target'] == 'redirect'){
  header('Location: '.$match['name']);
}
elseif(is_callable( $match['target'] ) ) {
  call_user_func_array( $match['target'], $match['params'] );
}
else{
  list( $controller, $action ) = explode( '#', $match['target'] );
  if ( is_callable(array($controller, $action)) ) {
    $obj = new $controller();
    call_user_func_array(array($obj,$action), array($match['params'],$match['name']));
  }
  else{
    // here your routes are wrong.
    // Throw an exception in debug, send a  500 error in production
  }
}