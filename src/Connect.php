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
  
  public function Query(
    string $sql
  ): Connect {
    try {
      if($this->Start()){
        $this->handleState = (
          $this->handle->query(
            $sql
          )
        );  
      }
    } catch(PDOException $e) {
      Message::Error(
        LogType::Database, $e->getMessage()
      );
    }

    return $this;    
  }

  public function Exec(
    string $sql
  ): bool {
    try {
      if($this->Start()){
        $this->handle->exec(
          $sql
        );
        
        return true;  
      }

      return false;
    } catch(PDOException $e){
      return Message::Error(
        LogType::Database, $e->getMessage()
      );
    }
  }  

  public function All(
  ): DataList {
    if( isset( $this->handleState )){
      return DataList::Create(
        $this->handleState->fetchAll()
      );
    }

    return DataList::Create();
  }
}