<?php

/**
 * To-do list application main class.
 */
class TodoApp {
  protected $dbName;
  protected $dbUser;
  protected $dbPass;
  protected $dbHost;
  protected $dbConnection;

  function __construct() {
  }
  
  /**
   * Set MySQL database name for connection.
   * 
   * @param string $name
   * @return void
   */
  public function setDatabaseName(string $name): void {
    $this->dbName = $name;
  }
  
  /**
   * Set MySQL database user for connection.
   *
   * @param string $user
   * @return void
   */
  public function setDatabaseUser(string $user): void {
    $this->dbUser = $user;
  }
  
  /**
   * Set MySQL database pass for connection.
   *
   * @param string $pass
   * @return void
   */
  public function setDatabasePass(string $pass): void {
    $this->dbPass = $pass;
  }
  
  /**
   * Set MySQL database host for connection.
   *
   * @param string $host
   * @return void
   */
  public function setDatabaseHost(string $host): void {
    $this->dbHost = $host;
  }

  /**
   * Checks if configuration for connection to database is complete.
   *
   * @return boolean
   */
  protected function isConfigComplete(): bool {
    return isset($this->dbName) && isset($this->dbUser) && isset($this->dbPass) && isset($this->dbHost);
  }

  /**
   * Tries to create connection to database, returns 503 response when failed.
   *
   * @return void
   */
  public function connectToDatabase(): void {

    if ( !$this->isConfigComplete() ) {
      Response::json(['error' => 'Imcomplete or missing configuration for database connection'], 503);
    }

    try {

      $this->dbConnection = new PDO(sprintf('mysql:host=%s;dbname=%s', $this->dbHost, $this->dbName), $this->dbUser, $this->dbPass);
      $this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      Response::json(['success' => 'Connected to database'], 200);

    } catch ( PDOException $e ) {

      Response::json(['error' => sprintf('Error during connection to database: %s', $e->getMessage())], 503);

    }

  }
}
