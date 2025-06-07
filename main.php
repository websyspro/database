<?php

use Websyspro\Database\Connect;

print_r(Connect::Set("shop"));
$rows1 = Connect::Set("shop")->Query("select * from Box")->All();
// print_r($rows1);