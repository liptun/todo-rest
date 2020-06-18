<?php

/**
 * Helper class for sending responses.
 */
class Response {
  /**
   * Sends json response with specified status code
   * and stops execution.
   *
   * @param array $data
   * @param integer $status
   * @return void
   */
  static function json(array $data, int $status = 200): void {
    header('Content-Type: application/json');
    http_response_code($status);
    echo json_encode($data);
    die;
  }
}
