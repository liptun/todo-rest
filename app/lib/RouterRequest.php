<?php

class RouterRequest {
  protected $method;
  protected $path;
  protected $action;
  protected $params = array();

  function __construct(string $method, string $path, $action) {
    $this->method = strtoupper($method);
    $this->path = $path;
    $this->action = $action;
  }

  /**
   * Calls defined callback function
   *
   * @return void
   */
  public function doAction(): void {
    call_user_func_array($this->action, [$this->params]);
    die;
  }

  /**
   * Check defined action path with request path with params
   * Params are automatic parsed to callback parameter
   *
   * @param array $request
   * @return boolean
   */
  public function checkRequest(array $request): bool {
    if ( strtoupper($request['method']) !== $this->method ) {
      return false;
    }
    $requestedPath = array_values(array_filter(explode('/', $request['path'])));
    $definedPath = array_values(array_filter(explode('/', $this->path)));
    if ( count($requestedPath) !== count($definedPath) ) {
      return false;
    }
    for ($i = 0; $i < count($definedPath); $i++) {
      if ( $definedPath[$i][0] === ':' ) {
        $this->params[ltrim($definedPath[$i], ':')] = is_numeric($requestedPath[$i])
          ? (int) $requestedPath[$i]
          : $requestedPath[$i]
        ;
      } else if ( $definedPath[$i] !== $requestedPath[$i] ) {
        return false;
      }
    }
    return true;
  }


}
