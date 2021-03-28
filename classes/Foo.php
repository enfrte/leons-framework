<?php 

class Foo {
  public function bar($foo = "", $bar = "")
  {
    echo $foo;
    echo $bar;
    var_dump($_SESSION['req']);
    return 'Foo controller';
  }
}