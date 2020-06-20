<?php

class RouterRequestData
{
    protected $method;
    protected $headers;
    protected $params;
    protected $body;

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }
    public function setHeadersArray(?array $headers = null): void
    {
        $this->headers = $headers;
    }
    public function setParamsArray(?array $params = null): void
    {
        $this->params = $params;
    }
    public function setBodyArray(?array $body = null): void
    {
        $this->body = $body;
    }

    /**
     * Get param by key
     *
     * @param string $name
     * @return string|null
     */
    public function getParam(string $name): ?string
    {
        return isset($this->params[$name])
        ? $this->params[$name]
        : null
        ;
    }

    /**
     * Get body by key
     *
     * @param string $name
     * @return mixed
     */
    public function getBody(string $name)
    {
        return isset($this->body[$name])
        ? $this->body[$name]
        : null
        ;
    }
}
