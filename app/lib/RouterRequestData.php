<?php

class RouterRequestData {
  protected $method;
  protected $headers;
  protected $params;
  protected $body;

  function __construct() {}

  public function setMethod(string $method): void {
    $this->method = $method;
  }
  public function setHeaders(?array $headers = null): void {
    $this->headers = $headers;
  }
  public function setParams(?array $params = null): void {
    $this->params = $params;
  }
  public function setBody(?array $body = null): void {
    $this->body = $body;
  }

  public function body(string $name = '') {
    return $name
      ? $this->body[$name]
      : $this->body
    ;
  }
}
