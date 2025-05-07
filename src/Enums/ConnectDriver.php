<?php

namespace Websyspro\Database\Enums;

enum ConnectDriver: string {
  case MySQL = "mysql";
  case Postgres = "pgsql";
  case SQLServer = "sqlsrv";
  case DBLib = "dblib";
}