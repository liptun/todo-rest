<?php

/**
 * Router
 */
class Router
{
    protected string $baseUrl;
    protected array $actions = array();

    /**
     * Set url base
     *
     * @param string $baseURL
     * @return void
     */
    public function setBaseUrl(string $baseURL): void
    {
        $this->baseUrl = $baseURL;
    }

    /**
     * Adds action to perferm when specified url is call
     *
     * @param string $method
     * @param string $path
     * @param $callback
     * @return void
     */
    public function addAction(string $method, string $path, $callback): void
    {
        $this->actions[] = new RouterRequest($method, $path, $callback);
    }

    /**
     * Check all defined actions and run matched
     *
     * @return void
     */
    public function start(): void
    {
        foreach ($this->actions as $action) {
            if ($action->checkRequest($this->getRequestPath())) {
                $action->doAction();
            }
        }
    }

    /**
     * Get request path without url base
     *
     * @return string
     */
    protected function getRequestPath(): string
    {
        return str_replace($this->baseUrl, '', $_SERVER['REQUEST_URI']);
    }

}
