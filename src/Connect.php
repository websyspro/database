<?php

namespace Websyspro\Database;

use PDO;
use PDOException;
use PDOStatement;
use Websyspro\Commons\DataList;
use Websyspro\Database\Interfaces\IDnsProps;
use Websyspro\Database\Shareds\DnsList;
use Websyspro\Logger\Enums\LogType;
use Websyspro\Logger\Message;

class Connect
{
  private PDO $handle;
  private PDOStatement $handleState;
  private int $lastId;

  public function __construct(
    private IDnsProps $dnsProps
  ){}

  public static function set(
    string | null $dns = null
  ): Connect | null {
    Connect::dnsList();

    if($dns === null){
      return new static(
        new IDnsProps(
          DnsList::dns(
            "dns-default"
          )->value
        )
      );
    }
    
    return new static(
      new IDnsProps(
        DnsList::dns(
          "dns-{$dns}"
        )->value
      )
    );
  }

  private static function dnsList(
  ): void {
    if(isset(DnsList::$dnsList) === false){
      DnsList::load();
    }
  }

  private function start(
  ): bool {
    try {
      $this->handle = new PDO(
        $this->dnsProps->text, 
        $this->dnsProps->user, 
        $this->dnsProps->pass
      );

      return true;
    } catch (PDOException $e){
      return Message::error(
        LogType::database, $e->getMessage() . " - " . $this->dnsProps->pass
      );
    }
  }

  public function database(
  ): string {
    $dnsPaths = DataList::create(
      preg_split( "/:/", $this->dnsProps->text, 2 )
    );

    $dnsPathsVars = DataList::create(
      preg_split("/;/", $dnsPaths->Last())
    );
    
    $dnsPathsVars->mapper(
      fn(string $var) => preg_split("/=/", $var)
    );

    $dnsPathsVars->where(
      fn(array $var) => in_array(
        reset($var), [
          "dbname", "Database"
        ]
      )
    );

    return DataList::create(
      $dnsPathsVars->first()
    )->last();
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
    } catch(PDOException $e){
      return Message::error(
        LogType::database, $e->getMessage()
      );
    }
  }
}