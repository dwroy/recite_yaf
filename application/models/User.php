<?php


class UserModel extends BaseModel
{

    protected $id;

    protected $name;

    protected $email;

    protected $passwd;

    protected $salt;

    public function __construct() 
    {
        parent::__construct('user');
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getPasswd()
    {
        return $this->passwd;
    }

    public function setPasswd($passwd)
    {
        $this->salt = uniqid('', true);
        $this->passwd = sha1($passwd.$this->salt);

        return $this;
    }

    public function checkPasswd($passwd)
    {
        return sha1($passwd.$this->salt) === $this->passwd;
    }

    public function save()
    {
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'passwd' => $this->passwd,
            'salt' => $this->salt
        ];

        $this->id ? $this->update($data, '`id`='.$this->id) : 
            $this->id = $this->insert($data);
    }

    protected function initContent($data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->passwd = $data['passwd'];
        $this->salt = $data['salt'];
    }
}