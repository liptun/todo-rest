<?php

/**
 * Dump variable with optional title
 * @param [type] $data
 * @param string $title
 * @return void
 */
function dump($data = null, $title = ''): void {
?>
<style>
  .styled-dump {
    background-color: #f3f3f3;
    color: #444;
    padding: 15px;
    display: block;
    position: relative;
    font-size: 16px;
    font-family: monospace;
  }
  .styled-dump__title {
    color: #d40000;
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 10px;
    display: block;
  }
  .styled-dump__content {
    white-space: pre-wrap;
  }
  .styled-dump:before {
    content: '';
    display: block;
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    width: 6px;
    background-color: #d40000;
  }
</style>
<?php
  echo '<pre class="styled-dump">';
  if ($title) {
    printf('<span class="styled-dump__title">%s</span>', $title);
  }
  if (is_array($data)) {
    array_walk_recursive($data, function (&$item) {
      $item = is_string($item) ? htmlspecialchars($item) : $item;
    });
  } else {
    $data = is_string($data) ? htmlspecialchars($data) : $data;
  }
  ob_start();
  var_dump($data);
  $dump_result = ob_get_clean();
  printf('<span class="styled-dump__content">%s</span>', $dump_result);
  echo '</pre>';
}

function d($data = null, $title = ''): void {
  dump($data, $title);
}

/**
 * Dump & Die
 * @param mixed $data
 */
function dd($data = null, $title = ''): void {
  dump($data, $title);
  die;
}