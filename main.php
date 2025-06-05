<?php

use Websyspro\Database\Connect;

$rows1 = Connect::Set("crm")->Query("select Id, Nome from Cargo")->All();
print_r($rows1);