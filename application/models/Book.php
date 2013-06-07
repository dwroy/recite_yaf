<?php

class BookModel extends BaseModel
{
    public function __construct() 
    {
        parent::__construct('book');
    }
}

class BookEntity
{
    protected $id;

    protected $title;

    protected $description;

    protected $size;

    protected $level;

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title= $title;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description= $description;

        return $this;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    protected function initContent($data)
    {
        $this->id = $data['id'];
        $this->title = $data['title'];
        $this->description = $data['description'];
        $this->size = $data['size'];
        $this->level = $data['level'];

        return $this;
    }
}