<?php

namespace Websyspro\Database\Shareds;

use Websyspro\Commons\DataList;
use Websyspro\Database\Interfaces\IDns;

class DnsList
{
  public static DataList $dnsList;

  public static function Load(
  ): DnsList {
    DnsList::$dnsList = DataList::Create(
      file( rootdir . DIRECTORY_SEPARATOR . ".env" )
    )->Mapper(
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
    return DnsList::$dnsList->Copy()->Where(
      fn(IDns $dns) => $dns->name === (
        $name ?? "dns-default"
      )
    )->First();
  }
}