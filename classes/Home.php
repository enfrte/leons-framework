<?php 

class Home {
  public function index($foo = "", $bar = "")
  {
    echo $foo;
    echo $bar;
    var_dump($_SESSION['req']);
    return 'Default controller';
  }
}