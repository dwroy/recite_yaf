<?php


class UserModel extends BaseModel
{
    public function __construct() 
    {
        parent::__construct('user');
    }

    public function findOneBy($criteria)
    {
        $row = $this->fetch($criteria);

        if($row)
        {
            $entity = (new UserEntity)->initContent($row);
            $this->cache($entity->id, $entity);

            return $entity;
        }

        return null;
    }

    public function find($id)
    {
        $entity = $this->fromCache($id);

        if($entity) return $entity;

        return $this->findOneBy(['id' => $id]);
    }

    protected function findBy($criteria, $order = null, $limit = 0, $offset = 0)
    {
        $rows = $this->fetchAll($criteria, $order, $limit, $offset);
    }

    public function save(UserEntity $entity)
    {
        $data = [
            'name' => $entity->name,
            'email' => $entity->email,
            'passwd' => $entity->passwd,
            'salt' => $entity->salt
        ];

        if($entity->id)
            $this->update($data, '`id`='.$entity->id);
        else
            $entity->id = $this->insert($data);

        $this->cache($entity->id, $entity);
    }

    public function newEntity()
    {
        return new UserEntity;
    }
}

class UserEntity
{
    protected $id;

    protected $name;

    protected $email;

    protected $passwd;

    protected $salt;

    public function __get($name) 
    {
        return $this->{'get'.$name}();
    }

    public function __set($name, $value)
    {
        $this->{'set'.$name}($value);
    }

    public function setId($id)
    {
        return $this->id = $id;

        return $this;
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

    public function getSalt()
    {
        return $this->salt;
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

    public function getAuthorizedKey($time)
    {
        return sha1($this->passwd.$this->salt.$time).'_'.$time.'_'.$this->id;
    }

    public function initContent($data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->passwd = $data['passwd'];
        $this->salt = $data['salt'];

        return $this;
    }
}