<?php

namespace Websyspro\Database\Interfaces;

class IDns
{
  public function __construct(
    public string $name,
    public string $value
  ){}
}