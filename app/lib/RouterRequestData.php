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

  /**
   * Get query param or body data depends on request method
   *
   * @param string $name
   * @return void
   */
  public function param(string $name) {
    if ( $this->method === 'GET' ) {
      return isset($this->params[$name])
        ? $this->params[$name]
        : null
      ;
    } else {
      return isset($this->body[$name])
        ? $this->body[$name]
        : null
      ;
    }
  }
}
