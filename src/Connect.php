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

  public static function Set(
    string | null $dns = null
  ): Connect | null {
    Connect::DnsList();

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

  private static function DnsList(
  ): void {
    if(isset( DnsList::$dnsList ) === false){
      DnsList::Load();
    }
  }

  private function Start(
  ): bool {
    try {
      $this->handle = new PDO(
        $this->dnsProps->text, 
        $this->dnsProps->user, 
        $this->dnsProps->pass, [
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
      );

      return true;
    } catch (PDOException $e){
      return Message::Error(
        LogType::Database, $e->getMessage()
      );
    }
  }

  public function Database(
  ): string {
    $dnsPaths = DataList::Create(
      preg_split( "/:/", $this->dnsProps->text, 2 )
    );

    $dnsPathsVars = DataList::Create(
      preg_split("/;/", $dnsPaths->Last())
    );
    
    $dnsPathsVars->Mapper(
      fn(string $var) => preg_split("/=/", $var)
    );

    $dnsPathsVars->Where(
      fn(array $var) => in_array(
        reset($var), [
          "dbname", "Database"
        ]
      )
    );

    return end($dnsPathsVars->First());
  }
  
  public function Query(
    string $sql
  ): DataList {
    try {
      if($this->Start()){
        $this->handleState = (
          $this->handle->query(
            $sql
          )
        ); 
        
        if( isset( $this->handleState )){
          return DataList::Create(
            $this->handleState->fetchAll()
          );
        }
      }
    } catch(PDOException $e) {
      return DataList::Create();
    }

    return DataList::Create();
  }

  public function Exec(
    string $sql
  ): bool|int {
    try {
      if($this->Start()){
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
      return Message::Error(
        LogType::Database, $e->getMessage()
      );
    }
  }
}