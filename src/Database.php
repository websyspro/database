<?php

namespace Websyspro\Database
{
  use Exception;

  class Database
  {
    private object $handle;
    private mixed $record;
    private string $error;

    public function __construct(
      private string | array $commandSql,
    ){
      $this->report();
      $this->connect();
      $this->execute();
    }

    private function report(
    ): void {
      mysqli_report(
        MYSQLI_REPORT_ERROR | 
        MYSQLI_REPORT_STRICT
      );
    }

    private function connect(
    ): void {
      try{
        $this->handle = mysqli_connect(
          "localhost", "root", "12345678", "sso", "3307"
        );
      } catch( Exception $_ ) {
        $this->error = mysqli_connect_error();
      }
    }

    public function hasError(
    ): bool {
      return empty($this->error) === false;
    }
    
    public function ObterError(
    ): string {
      return $this->error;
    }

    public function Execute(
    ): void {
      if ($this->hasError() === false){
        try {
          if (is_array($this->commandSql)) {
            if(sizeof($this->commandSql) !== 0) {
              $this->record = mysqli_multi_query(
                $this->handle, implode(";", $this->commandSql)
              );
            }
          } else {
            $this->record = mysqli_query(
              $this->handle, $this->commandSql
            );
          }
        } catch (Exception $error ) {
          $this->error = mysqli_error(
            $this->handle
          );
        }
      }
    }

    public function ObterRows(
    ): array {
      if ($this->record === false) {
        return [];
      }

      if ( mysqli_num_rows($this->record) === 0) {
        return [];
      }

      return mysqli_fetch_all(
        $this->record, MYSQLI_ASSOC
      );
    }

    public static function query(
      string | array $commandSql
    ): Database {
      return new static(
        commandSql: $commandSql
      );
    }

    public function ObterLastId(): int {
      return mysqli_insert_id(
        $this->handle
      );
    }
  }
}