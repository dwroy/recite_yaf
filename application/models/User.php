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

    public static function currentUser()
    {

    }
}