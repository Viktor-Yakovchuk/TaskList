<?php
namespace TaskListApp;

/**
 * TasksList includes Tasks
 every Task consists of user, email, text, picture
 *
 */

final class TaskList implements \Iterator
{
    public function addTask(Task $task)
    {
        $this->saveTask($task);
        $this->tasks[$task->getId()] = $task;
    }

    private function saveTask(Task $task)
    {
        $db = \DBConnection::get();
        $db->query("INSERT INTO `tasks` (`username`, `email`, `text`) VALUES (?s, ?s, ?s)", $task->getUsername(), $task->getEmail(), $task->getText());
        $task->setId($db->insertId());
    }

    //public function Task getTask($id) //only in php7 //public function getTask($id): Task
    public function getTask($id)
    {
        if (!isset($this->tasks[$id]))
            throw new Exceptions\TaskNotFoundException(TASKNOTFOUND . $id);

        return $this->tasks[$id];
    }

    //private $position = 0;
    private $tasks = array();  

    public function __construct()
    {
        $this->rewind();
        
        $this->loadFromDb();
    }

    public function loadFromDb()
    {
        $db = \DBConnection::get();
        $res = $db->query("SELECT * FROM `tasks`");

        while ($row = $db->fetch($res))
            $this->tasks[$row['id']] = new Task($row);

        $db->free($res);
    }

    public function getAllTasks()
    {
        return array_values($this->tasks);
    }

    public function rewind()
    {
        //$this->position = 0;
        reset($this->tasks);
    }

    public function current()
    {
        //return $this->tasks[$this->position];
        return current($this->tasks);
    }

    public function key()
    {
        //return $this->position;
        return key($this->tasks);
    }

    public function next()
    {
        //++$this->position;
        next($this->tasks);
    }

    public function valid()
    {
        return $this->current() !== FALSE;
    }

    public function __toString()
    {
        return print_r($this, true);
    }
}
