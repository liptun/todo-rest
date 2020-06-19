<?php

/**
 * Helper class for sending responses.
 */
class Response {
  /**
   * Sends json response with specified status code
   * and stops execution.
   *
   * @param $data
   * @param integer $status
   * @return void
   */
  public static function json($data, int $status = 200): void {
    header('Content-Type: application/json');
    http_response_code($status);
    if (is_array($data)) {
      echo json_encode($data);
    } else {
      echo json_encode(array($data));
    }
    die;
  }
}
