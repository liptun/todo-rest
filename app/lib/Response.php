<?php

class Response {
  static function json(array $data, int $status = 200): void {
    header('Content-Type: application/json');
    http_response_code($status);
    echo json_encode($data);
    die;
  }
}
