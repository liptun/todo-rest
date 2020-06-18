<?php

/**
 * Helper class for parsing requests
 */
class Request {
  protected $method;
  protected $path;
  protected $currentPathIndex = 0;

  function __construct(string $method, string $path, string $rootURL = '') {
    $this->method = $method;
    $this->path = explode('/', str_replace($rootURL, '', $path));
  }

  /**
   * Returns method type
   *
   * @return string
   */
  public function getMethod(): string {
    return $this->method;
  }

  /**
   * Return parset param by index
   *
   * @param integer $index
   * @return void
   */
  public function getParam(int $index = 0) {
    $this->currentPathIndex = $index;
    return $this->parseParam($this->path[$this->currentPathIndex]);
  }

  /**
   * Returns next param relative to last getParam call
   *
   * @return void
   */
  public function getParamNext() {
    $nextPathIndex = $this->currentPathIndex + 1;
    return $this->isParamExists($nextPathIndex)
      ? $this->getParam($nextPathIndex)
      : null
    ;
  }

  /**
   * Cast to integer if numeric
   *
   * @param string $param
   * @return void
   */
  protected function parseParam(string $param) {
    return is_numeric($param)
      ? (int) $param
      : $param
    ;
  }

  /**
   * Checks if param of specified index exists
   *
   * @param integer $index
   * @return boolean
   */
  protected function isParamExists(int $index): bool {
    return isset($this->path[$index]);
  }

}
