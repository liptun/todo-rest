<?php

class RouterRequest {
  protected $method;
  protected $path;
  protected $callback;
  protected $params = array();
  protected $body = array();
  protected $headers = array();

  
  function __construct(string $method, string $path, $callback) {
    $this->method = strtoupper($method);
    $this->path = $path;
    $this->callback = $callback;
  }

  /**
   * Calls defined callback function
   *
   * @return void
   */
  public function doAction(): void {
    
    $requestData = new RouterRequestData;
    $requestData->setMethod($this->method);
    $requestData->setHeadersArray($this->headers);
    $requestData->setParamsArray($this->params);
    $requestData->setBodyArray($this->body);

    call_user_func_array($this->callback, ['req' => $requestData]);
    exit;
  }

  /**
   * Check defined action path with request path with params
   * Params are automatic parsed to callback parameter
   *
   * @param array $request
   * @return boolean
   */
  public function checkRequest(string $requestPath = ''): bool {
    $request = array(
      'method' => $_SERVER['REQUEST_METHOD'],
      'path' => isset($requestPath)
        ? $requestPath
        : $_SERVER['REQUEST_URI'],
    );

    // Compare request methods
    if ( strtoupper($request['method']) !== $this->method && $this->method !== '*' ) {
      return false;
    }

    if ( $this->path === '*' ) {
      return true;
    }

    // Parse query url params
    $requestUrl = parse_url($request['path']);

    if ( isset($requestUrl['query']) ) {
      parse_str($requestUrl['query'], $params);
      foreach ( $params as $paramKey => $paramValue ) {
        $this->params[$paramKey] = $paramValue;
      }
    }

    // Get all headers
    foreach ( getallheaders() as $key => $value ) {
      $this->headers[ucfirst(strtolower($key))] = $value;
    }

    // Parse params or body content
    if ( $this->method === 'GET' ) {
      $this->params = $_GET;
    } else {
      $this->body = $this->getRequestBodyJSON();
    }

    $requestedPath = array_values(array_filter(explode('/', $requestUrl['path'])));
    $definedPath = array_values(array_filter(explode('/', $this->path)));

    // Check number of elements in request path
    if ( count($requestedPath) !== count($definedPath) ) {
      return false;
    }

    // Compare path elements
    for ($i = 0; $i < count($definedPath); $i++) {
      if ( $definedPath[$i][0] === ':' ) {
        $this->params[ltrim($definedPath[$i], ':')] = $requestedPath[$i];
      } else if ( $definedPath[$i] !== $requestedPath[$i] ) {
        return false;
      }
    }

    return true;
  }

  /**
   * Get parsed JSON body of request
   *
   * @return array|null
   */
  private function getRequestBodyJSON(): ?array {
    return json_decode(file_get_contents('php://input'), true);
  }

}
