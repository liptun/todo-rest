<?php

/**
 * Helper class for sending responses.
 */
class Response
{
    /**
     * Sends json response with specified status code
     * and stops execution.
     *
     * @param string|array $data
     * @param integer $status
     * @return void
     */
    public static function json($data, int $status = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo is_array($data)
            ? json_encode($data)
            : json_encode(array($data))
        ;
        exit;
    }

    public static function error(string $errorMessage, int $status = 500): void
    {
        Response::json(['error' => $errorMessage], $status);
    }
}
