<?php

/**
 * To-do list application main class.
 */
class TodoApp {
  protected $dbName;
  protected $dbUser;
  protected $dbPass;
  protected $dbHost;
  protected $db;

  public function run(): void {
    $this->connectToDatabase();
    $this->parseRequest();
  }

  public function setDatabaseName(string $name): void {
    $this->dbName = $name;
  }
  public function setDatabaseUser(string $user): void {
    $this->dbUser = $user;
  }
  public function setDatabasePass(string $pass): void {
    $this->dbPass = $pass;
  }
  public function setDatabaseHost(string $host): void {
    $this->dbHost = $host;
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
      $this->db = new PDO(sprintf('mysql:host=%s;dbname=%s', $this->dbHost, $this->dbName), $this->dbUser, $this->dbPass);
      $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch ( PDOException $e ) {
      Response::json(['error' => sprintf('Error during connection to database: %s', $e->getMessage())], 503);
    }

    $this->prepareDatabase();

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
   * Create application tables structure
   *
   * @return void
   */
  protected function prepareDatabase(): void {
    $sql = 'CREATE TABLE IF NOT EXISTS `todo_tasks` (
      id INT PRIMARY KEY AUTO_INCREMENT,
      project INT NOT NULL,
      user INT NOT NULL,
      name varchar(255) NOT NULL,
      description text(512) NOT NULL,
      status boolean NOT NULL
    );';
    $query = $this->db->prepare($sql);
    $query->execute();

    $sql = 'CREATE TABLE IF NOT EXISTS `todo_projects` (
      id INT PRIMARY KEY AUTO_INCREMENT,
      user INT NOT NULL,
      name varchar(255) NOT NULL,
      description text(512) NOT NULL
    );';
    $query = $this->db->prepare($sql);
    $query->execute();

    $sql = 'CREATE TABLE IF NOT EXISTS `todo_users` (
      id INT PRIMARY KEY AUTO_INCREMENT,
      name varchar(255) NOT NULL,
      email varchar(255) NOT NULL
    );';
    $query = $this->db->prepare($sql);
    $query->execute();
  }

  protected static function modelTask(array $taskRawData): array {
    return array(
      'id' => (int) $taskRawData['id'],
      'name' => $taskRawData['name'],
      'description' => $taskRawData['description'],
      'project' => (int) $taskRawData['project'],
      'status' => (bool) $taskRawData['status']
    );
  }

  protected function parseRequest(): void {

    $router = new Router();
    $router->setBaseUrl('/api/v1');

    $router->addAction('GET', '/', function($req){
      Response::json([
        'name' => 'todo-rest',
        'description' => 'This is simple CRUD TODO list application',
        'author' => 'Rafał Karczmarzyk',
        'docs' => '#',
        'url' => '#'
      ]);
    });

    $router->addAction('GET', '/tasks', function($req){

      $sql = 'SELECT * from `todo_tasks`;';

      $query = $this->db->prepare($sql);
      $query->execute();

      $responseData = array();
      foreach( $query->fetchAll() as $queryRow ) {
        $responseData[] = TodoApp::modelTask($queryRow);
      }
      Response::json($responseData);
    });

    $router->addAction('GET', '/tasks/:id', function($req){
      $data = array(
        'id' => $req->getParam('id'),
        'name' => sprintf('Task %s', $req->getParam('id')),
        'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit.',
        'status' => false,
        'list' => 0
      );
      Response::json($data);
    });

    $router->addAction('POST', '/tasks', function($req){
      $data = array(
        'name' => $req->getBody('name'),
        'description' => $req->getBody('description'),
        'status' => $req->getBody('status'),
        'list' => 0
      );
      Response::json($data, 201);
    });

    $router->addAction('*', '*', function($req){
      Response::json(['Bad request'], 400);
    });

    $router->start();

  }

}