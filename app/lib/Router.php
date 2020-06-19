<?php

/**
 * Router
 */
class Router {
  protected $method;
  protected $path;
  protected $baseUrl;
  protected $currentPathIndex = 0;
  protected $actions = array();

  function __construct() {
    $this->method = $_SERVER['REQUEST_METHOD'];
    $this->path = $_SERVER['REQUEST_URI'];
  }

  /**
   * Set url base
   *
   * @param string $baseURL
   * @return void
   */
  public function setBaseUrl(string $baseURL): void {
    $this->baseUrl = $baseURL;
  }
  
  /**
   * Adds action to perferm when specified url is call
   *
   * @param string $method
   * @param string $path
   * @param [type] $callback
   * @return void
   */
  public function addAction(string $method, string $path, $callback): void {
    $this->actions[] = new RouterRequest($method, $path, $callback);
  }

  /**
   * Check all defined actions and run matched
   *
   * @return void
   */
  public function work(): void {

    foreach ( $this->actions as $action ) {
      if ( $action->checkRequest($this->getRequest()) ) {
        $action->doAction();
      }
    }
  }
  
  /**
   * Get request path without url base
   *
   * @return string
   */
  protected function getRequestPath(): string {
    return str_replace($this->baseUrl, '', $this->path);
  }

  /**
   * Returns method type
   *
   * @return string
   */
  protected function getRequestMethod(): string {
    return $this->method;
  }

  /**
   * Get reuqst array with path and method
   *
   * @return array
   */
  protected function getRequest(): array {
    return array(
      'method' => $this->getRequestMethod(),
      'path' => $this->getRequestPath()
    );
  }


}
