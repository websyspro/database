<?php

use Websyspro\Database\Connect;

print_r(Connect::Set("crm")->Database());
// $rows1 = Connect::Set("crm")->Query("select Id, Nome from Cargo")->All();
// print_r($rows1);