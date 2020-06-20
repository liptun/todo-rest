<?php

/**
 * To-do list application main class.
 */
class TodoApp
{
    protected $dbName;
    protected $dbUser;
    protected $dbPass;
    protected $dbHost;
    protected $db;

    public function run(): void
    {
        $this->connectToDatabase();
        $this->parseRequest();
    }

    public function setDatabaseName(string $name): void
    {
        $this->dbName = $name;
    }
    public function setDatabaseUser(string $user): void
    {
        $this->dbUser = $user;
    }
    public function setDatabasePass(string $pass): void
    {
        $this->dbPass = $pass;
    }
    public function setDatabaseHost(string $host): void
    {
        $this->dbHost = $host;
    }

    /**
     * Tries to create connection to database, returns 503 response when failed.
     *
     * @return void
     */
    public function connectToDatabase(): void
    {
        if (!$this->isConfigComplete()) {
            Response::error('Imcomplete or missing configuration for database connection', 503);
        }
        try {
            $this->db = new PDO(sprintf('mysql:host=%s;dbname=%s', $this->dbHost, $this->dbName), $this->dbUser, $this->dbPass);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            Response::error(sprintf('Error during connection to database: %s', $e->getMessage()), 503);
        }
        $this->prepareDatabase();
    }

    /**
     * Checks if configuration for connection to database is complete.
     *
     * @return boolean
     */
    protected function isConfigComplete(): bool
    {
        return isset($this->dbName) && isset($this->dbUser) && isset($this->dbPass) && isset($this->dbHost);
    }

    /**
     * Create application tables structure
     *
     * @return void
     */
    protected function prepareDatabase(): void
    {
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

    protected static function modelTask(array $taskRawData): array
    {
        return array(
            'id' => (int) $taskRawData['id'],
            'name' => $taskRawData['name'],
            'description' => $taskRawData['description'],
            'project' => (int) $taskRawData['project'],
            'user' => (int) $taskRawData['user'],
            'status' => (bool) $taskRawData['status'],
        );
    }

    protected static function filterText(string $input): string
    {
        $input = htmlspecialchars($input);
        $input = trim($input);
        return $input;
    }

    protected function getAllTasks(): array
    {
        $sql = 'SELECT * from `todo_tasks`;';
        $query = $this->db->prepare($sql);
        $query->execute();

        $responseData = array();
        $queryResult = $query->fetchAll();
        foreach ($queryResult as $queryRow) {
            $responseData[] = TodoApp::modelTask($queryRow);
        }
        return $responseData;
    }

    protected function getTask($id)
    {
        $sql = sprintf('SELECT * from `todo_tasks` WHERE id=%s;', $id);
        $query = $this->db->prepare($sql);
        $query->execute();

        $queryResult = $query->fetch();
        return $queryResult
        ? TodoApp::modelTask($queryResult)
        : false
        ;
    }

    protected function responseTask($id): void
    {
        $queryResult = $this->getTask($id);
        if ($queryResult) {
            Response::json($queryResult);
        } else {
            Response::error(sprintf('Item with id: %s don\'t exists.', $id), 404);
        }
    }

    protected function postTask(array $newTaskData)
    {

        if (empty($newTaskData['description'])) {
            $newTaskData['description'] = '';
        }
        if (empty($newTaskData['status'])) {
            $newTaskData['status'] = false;
        }
        if (empty($newTaskData['project'])) {
            $newTaskData['project'] = 0;
        }
        if (empty($newTaskData['user'])) {
            $newTaskData['user'] = 0;
        }

        $newTaskData['name'] = TodoApp::filterText($newTaskData['name']);
        $newTaskData['description'] = TodoApp::filterText($newTaskData['description']);

        $sql = "INSERT INTO `todo_tasks`(name, description, status, project, user) VALUES (:name, :description, :status, :project, :user)";
        $query = $this->db->prepare($sql);
        $query->bindParam(':name', $newTaskData['name'], PDO::PARAM_STR);
        $query->bindParam(':description', $newTaskData['description'], PDO::PARAM_STR);
        $query->bindParam(':status', $newTaskData['status'], PDO::PARAM_BOOL);
        $query->bindParam(':project', $newTaskData['project'], PDO::PARAM_INT);
        $query->bindParam(':user', $newTaskData['user'], PDO::PARAM_INT);

        if ($query->execute()) {
            return $this->getTask($this->db->lastInsertId());
        } else {
            return false;
        }
    }

    protected function deleteTask($id)
    {
        $sql = 'DELETE FROM `todo_tasks` WHERE `id` = :id';
        $query = $this->db->prepare($sql);
        $query->bindParam(':id', $id);
        return $query->execute();
    }

    protected function parseRequest(): void
    {

        $router = new Router();
        $router->setBaseUrl('/api/v1');

        $router->addAction('GET', '/', function ($req) {
            Response::json([
                'name' => 'todo-rest',
                'description' => 'This is simple CRUD TODO list application',
                'author' => 'Rafał Karczmarzyk',
                'docs' => '#',
                'url' => '#',
            ]);
        });

        $router->addAction('GET', '/tasks', function ($req) {
            if ($req->getParam('id')) {
                $this->responseTask($req->getParam('id'));
            } else {
                Response::json($this->getAllTasks());
            }
        });
        $router->addAction('GET', '/tasks/:id', function ($req) {
            $this->responseTask($req->getParam('id'));
        });

        $router->addAction('POST', '/tasks', function ($req) {

            if ($req->getBody('name') === null) {
                Response::error('name is reqired', 400);
            }
            if (!is_string($req->getBody('name'))) {
                Response::error('name must be a string', 400);
            }
            if ($req->getBody('description') !== null && !is_string($req->getBody('description'))) {
                Response::error('description must be a string', 400);
            }
            if ($req->getBody('status') !== null && !is_bool($req->getBody('status'))) {
                Response::error('status must be a boolean', 400);
            }
            if ($req->getBody('project') !== null && !is_int($req->getBody('project'))) {
                Response::error('project must be a integer', 400);
            }
            if ($req->getBody('user') !== null && !is_int($req->getBody('user'))) {
                Response::error('user must be a integer', 400);
            }

            $newTaskData = array(
                'name' => $req->getBody('name'),
                'description' => $req->getBody('description'),
                'status' => $req->getBody('status'),
                'project' => $req->getBody('project'),
                'user' => $req->getBody('user'),
            );
            $newTask = $this->postTask($newTaskData);
            if ($newTask) {
                Response::json($newTask, 201);
            } else {
                Response::error('There was an error during creating new task');
            }
        });

        $router->addAction('DELETE', '/tasks', function ($req) {

            if (!$req->getBody('id')) {
                Response::error('id is required', 400);
            }
            if (!is_int($req->getBody('id'))) {
                Response::error('id must be integer', 400);
            }
            if (!$this->getTask($req->getBody('id'))) {
                Response::error(sprintf('task with id %s not exists', $req->getBody('id')), 404);
            }

            if ($this->deleteTask($req->getBody('id'))) {
                Response::json(['success' => sprintf('Task with id %s was succesfully removed from database', $req->getBody('id'))]);
            } else {
                Response::error('There was an error during removing from database');
            }
        });

        $router->addAction('*', '*', function ($req) {
            Response::json(['Bad request'], 400);
        });

        $router->start();

    }

}
