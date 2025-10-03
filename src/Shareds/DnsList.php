<?php

namespace Websyspro\Database\Shareds;

use Websyspro\Commons\DataList;
use Websyspro\Database\Interfaces\IDns;

class DnsList
{
  public static DataList $dnsList;

  public static function load(
  ): DnsList {
    if(file_exists(rootdir . DIRECTORY_SEPARATOR . ".env") === false){
      DnsList::$dnsList = DataList::create([]);
      return new static;
    }

    DnsList::$dnsList = DataList::create(
      file( rootdir . DIRECTORY_SEPARATOR . ".env" )
    )->mapper(
      function(string $dns){
        [ $name, $text ] = explode('=', preg_replace("/\r?\n/", "", $dns), 2);

        return new IDns(
          $name, preg_replace("/(^\")|(\"$)/", "", $text)
        );
      }
    );

    return new static;
  }

  public static function dns(
    string | null $name = null
  ): IDns | null {
    $dns = DnsList::$dnsList
      ->copy()
      ->where(
        fn(IDns $dns) => $dns->name === (
          $name ?? "dns-default"
        )
      );

    return $dns->exist() 
      ? $dns->first() 
      : null;
  }
}