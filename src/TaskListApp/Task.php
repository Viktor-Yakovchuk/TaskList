<?php
namespace TaskListApp;

final class Task implements \JsonSerializable
{
    private $id = -1; //$result->last_insert_id

    private $username;
    private $email;
    private $text;
    private $status = 0;

    public function __construct(array $taskParameters)
    {
        $this->fromArray($taskParameters);
    }

    public function fromArray(array $taskParameters)
    {
        $fields = ['id', 'username', 'email', 'text', 'status'];

        foreach ($fields as $k)
            if (isset($taskParameters[$k]))
                $this->{'set' . ucfirst($k)}($taskParameters[$k]);
    }

    public function commit()
    {
        $db = \DBConnection::get();
        $db->query("UPDATE `tasks` SET `username` = ?s, `email` = ?s, `text` = ?s, `status` = ?i WHERE `id` = ?i", $this->getUsername(), $this->getEmail(), $this->getText(), $this->getStatus(), $this->getID());
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setEmail($email)
    {
        if (FALSE === filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new Exceptions\InvalidEmailException(INVALIDEMAIL);

        $this->email = $email;
    }
    
    public function getEmail()
    {
        return $this->email;
    }

    public function setText($text)
    {
        $this->text = $text;
    }
    
    public function getText()
    {
        return $this->text;
    }
    
    public function setId($id)
    {
        $this->id = (int)$id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function __toString()
    {
        return print_r($this, true);
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'text' => $this->text,
            'status' => $this->status
        ];
    }
}