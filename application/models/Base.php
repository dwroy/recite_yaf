<?php

class BaseModel 
{
	/**
	 * mysql database connection
	 */
	private $pdo;

	/**
	 * data table used as table table or cache key prefix
	 * @var string
	 */
	protected $table;

    protected $cache = [];

    protected static $models = [];

    public static function getInstance($name)
    {
         if(empty(BaseModel::$models[$name]))
         {
             $class = $name.'Model';
             BaseModel::$models[$name] = new $class;
         }

         return BaseModel::$models[$name];
    }

    protected function cache($key, $entity)
    {
        $this->cache[$key] = $entity;
    }

    public function __construct($table)
    {
        $this->table = $table;
    }

    /**
	 * get mysql connection
	 * @return mysql db connection
	 */
	protected function pdo()
	{
		if(empty($this->pdo))
		{
            $db = Yaf_Registry::get('config')->db;
			$this->pdo = new PDO($db->get('dsn'), $db->get('user'), $db->get('passwd'));
		}

		return $this->pdo;
	}

    protected function fromCache($key)
    {
        return isset($this->cache[$key]) ? $this->cache[$key] : null;
    }

    /**
	 * insert data
	 * @param array $data
	 * @return int 
	 */
	public function insert( $data )
	{
		$columns = $values = array();

		foreach( $data as $column => $value )
		{
			$columns[] = $column;
			$values[] = $value;
		}

        $this->pdo()->exec( 'insert into `' . $this->table . '` (`' . implode( '`,`' , $columns ) . '`) values ("' . implode( '","' , $values ) . '")' );

        return $this->pdo->lastInsertId();
	}

	/**
	 * update data 
	 * @param array $data
	 * @param string $where
	 */
	public function update( $data , $where )
	{
		$tmp = '';

		foreach( $data as $column => $value )
		{
			$tmp .= '`' . $column . '`="' . $value . '",';
		}

		return $this->pdo()->exec( 'update `' . $this->table . '` set ' . substr( $tmp , 0 , -1 ) . ' where ' . $where);
	}

	protected function delete( $where , $limit = '0,1' )
	{
		return $this->pdo()->exec( 'delete * from `' . $this->table . '` where ' . $where . ' ' . $limit );
	}

    public function fetch($criteria)
    {
        $sql = 'select * from `'.$this->table.'` where 1=1';
        foreach($criteria as $key => $value) $sql .= " and `$key`='$value'";
        $sql .= ' limit 0,1';

        return $this->pdo()->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchAll($criteria, $order = null, $limit = 0, $offset = 0)
    {
        $sql = 'select * from `'.$this->table.'` where 1=1';

        if($criteria)
            foreach($criteria as $key => $value) $sql .= " and `$key`='$value'";

        if(is_array($order))
        {
            list($column, $value) = each($order);
            $sql .= " order by $column $value";
        }

        if($limit) $sql .= ' limit '.$offset.', '.$limit;

        $result = $this->pdo()->query($sql);

        return $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];
    }

	public function count($where = '1=1')
	{
        $sql = 'select count(*) c from `'.$this->table.'` where '.$where;

		return $this->pdo()->query($sql)->fetch( PDO::FETCH_ASSOC )['c'];
	}
}