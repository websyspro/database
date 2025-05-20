<?php

namespace Websyspro\Database;

use PDO;
use PDOStatement;
use PDOException;
use Websyspro\Commons\Collection;

class TConnect
{
  private PDO $handle;
  private PDOStatement $handleState;

  public function __construct(
    public string | null $entityClass = null
  ){}

  private function On(
  ): bool {
    try {
      $this->handle = new PDO(
        "mysql:host=localhost;port=3307;dbname=Shop;charset=utf8mb4;", "root", "@Qazwsx190483", $this->Opts()
      );

      return true;
    } catch (PDOException $e){
      return false;
    }
  }

  private function Opts(
  ): array {
    return [
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];
  }  

  public function Get(
    string $query
  ): self {
    try {
      if($this->On()){
        $this->handleState = (
          $this->handle->query($query)
        );  
      }

    } catch(PDOException $e){}

    return $this;
  }

  public function Call(
    string $query
  ): bool {
    try {
      if($this->on()){
        $this->handle->exec($query);
        return true;  
      }

      return false;
    } catch(PDOException $e){
      return false;
    }
  }

  public function All(
  ): Collection {
    if($this->handleState){
      return new Collection(
        $this->handleState->fetchAll()
      );
    }

    return new Collection();
  }
}