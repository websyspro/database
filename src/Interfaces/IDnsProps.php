<?php

namespace Websyspro\Database\Interfaces;

class IDnsProps
{
  public string $text;
  public string $user;
  public string $pass;

  public function __construct(
    string $dns
  ){
    [ $this->text, $this->user, $this->pass 
    ] = explode("||", $dns);
  }
}