<?php

namespace Websyspro\Database\Interfaces;

class IDnsProps
{
  public string|null $text;
  public string|null $user;
  public string|null $pass;

  public function __construct(
    string $dns
  ){
    [ $this->text, $this->user, $this->pass 
    ] = explode("||", $dns);
  }
}