<?php

class RouterRequestData
{
    protected string $method;
    protected array $headers;
    protected array $params;
    protected array $body;

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
     * Get array of params
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }


    /**
     * Get all body data or specified value by providing key
     *
     * @param string|null $name
     * @return void
     */
    public function getBody(?string $key = null)
    {
        if (empty($key)) {
            return $this->body;
        } else {
            return isset($this->body[$key])
                ? $this->body[$key]
                : null
            ;
        }
    }
}
