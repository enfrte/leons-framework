<?php 

// Returns the key of the first match in the associated array. 
// Use :anything to indicate a method argument
$routes = [
    // 'Controller/Method' => 'URI address',
    'Home/index' => 'home',
    'Foo/bar' => 'news/:id/:id',
];

return $routes;
