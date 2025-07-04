<?php

namespace Websyspro\Database\Enums;

enum ConnectDriver: string {
  case mysql = "mysql";
  case postgres = "pgsql";
  case sqlServer = "sqlsrv";
  case dbLib = "dblib";
}