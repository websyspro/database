<?php

namespace Websyspro\Database;

use PDO;
use PDOException;
use PDOStatement;
use Websyspro\Commons\DataList;
use Websyspro\DynamicSql\Enums\EDriverType;
use Websyspro\Logger\Enums\LogType;
use Websyspro\Logger\Message;

class Connect
{
  private PDO $handle;
  private PDOStatement $handleState;
  private int $lastId;

  public function __construct(){}

  public static function set(
  ): Connect {
    return new static();
  }

  private function env(
    string $key
  ): string {
    return getenv(
      "DATABASE_{$key}"
    );
  }

  private function start(
  ): bool {
    try {
      $this->handle = new PDO(
        $this->getConnectStr(), 
        $this->env("USER"),
        $this->env("PASS")
      );

      return true;
    } catch (PDOException $e){
      return Message::error(
        LogType::database, $e->getMessage()
      );
    }
  }

  private function getConnectStr(
  ): string {
    if(in_array(strtolower($this->env("TYPE")), ["mysql"])){
      return "mysql:host={$this->env("HOST")};dbname={$this->env("NAME")};port={$this->env("PORT")};charset=utf8mb4";
    } else 
    if(in_array(strtolower($this->env("TYPE")), ["pg","pgsql"])){
      return "pgsql:host={$this->env("HOST")};dbname={$this->env("NAME")};port={$this->env("PORT")}";
    } else
    if(in_array(strtolower($this->env("TYPE")), ["sqlsrv"])){
      return "sqlsrv:Server={$this->env("HOST")},{$this->env("PORT")};Database={$this->env("NAME")}";
    } else 
    if(in_array(strtolower($this->env("TYPE")), ["dblib"])){
      return "dblib:host={$this->env("HOST")}:{$this->env("PORT")};dbname={$this->env("NAME")}";
    } else return "";
  }

  public function driverType(
  ): EDriverType {
    $driver = getenv(
      "DATABASE_DRIVE"
    );

    if($driver === "mysql")
      return EDriverType::mysql;
    if($driver === "pgsql")
      return EDriverType::postgress;
    if($driver === "sqlsrv")
      return EDriverType::sqlserver;
    if($driver === "dblib")
      return EDriverType::sqlserver;

    return EDriverType::mysql;
  }

  public function database(
  ): string {
    return getenv("DATABASE_NAME");
  }
  
  public function query(
    string $sql
  ): DataList {
    try {
      if($this->start()){
        $this->handleState = (
          $this->handle->query(
            $sql
          )
        ); 
        
        if( isset( $this->handleState )){
          return DataList::create(
            $this->handleState->fetchAll(
              PDO::FETCH_OBJ
            )
          );
        }
      }
    } catch(PDOException $e) {
      return DataList::create();
    }

    return DataList::create();
  }

  public function exec(
    string $sql
  ): bool|int {
    try {
      if($this->start()){
        $affectedRows = (
          $this->handle->exec(
            $sql
          )
        );

        if($affectedRows === 1){
          if(preg_match("/^insert/i", trim($sql)) === 1){
            return $this->handle->lastInsertId();
          }
        }
        
        return true;  
      }

      return false;
    } catch(PDOException $error){
      return Message::error(
        LogType::database, $error->getMessage()
      );
    }
  }
}