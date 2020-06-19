<?php

class RouterRequest {
  protected $method;
  protected $path;
  protected $action;
  protected $params = array();
  protected $body = array();

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
    $callbackData = array();
    if ( !empty($this->params) && $this->method === 'GET' ) {
      $callbackData['params'] = $this->params;
    }
    if ( !empty($this->body) && $this->method !== 'GET' ) {
      $callbackData['body'] = $this->body;
    }
    call_user_func_array($this->action, ['req' => $callbackData]);
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

    $requestUrl = parse_url($request['path']);

    if ( isset($requestUrl['query']) ) {
      parse_str($requestUrl['query'], $params);
      foreach ( $params as $paramKey => $paramValue ) {
        $this->params[$paramKey] = $paramValue;
      }
    }

    if ( $this->method === 'GET' ) {
      $this->params = $_GET;
    } else {
      $this->body = $this->getRequestBodyJSON();
    }

    $requestedPath = array_values(array_filter(explode('/', $requestUrl['path'])));
    $definedPath = array_values(array_filter(explode('/', $this->path)));

    if ( count($requestedPath) !== count($definedPath) ) {
      return false;
    }

    for ($i = 0; $i < count($definedPath); $i++) {
      if ( $definedPath[$i][0] === ':' ) {
        $this->params[ltrim($definedPath[$i], ':')] = $requestedPath[$i];
      } else if ( $definedPath[$i] !== $requestedPath[$i] ) {
        return false;
      }
    }

    return true;
  }

  private function getRequestBodyJSON(): ?array {
    return json_decode(file_get_contents('php://input'), true);
  }

}
