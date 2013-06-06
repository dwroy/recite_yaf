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

    public function __construct($table) 
    {
        $this->table = $table;
    }

    public function __get($name)
    {
        return $this->{'get'.$name}();
    }

    public function __set($name, $value)
    {
        $this->{'set'.$name}($value);
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

	/** 
	 * get cache connection
	 * @return Reids
	protected function initCache()
	{
		if( empty( $this->cache ) )
		{
			$this->cache = new Redis;
			$config = c( 'cache' );

			if( !$this->cache->pconnect( $config['host'] , $config['port'] ) )
			{
				throw new KernelException( t( 'cache disconnected' ) , KernelException::CACHE_DISCONNECTED );
			}
		}

		return $this->cache;
	}

	protected function cacheKey( $uniqueId )
	{
		return $this->table . '-' . $uniqueId;
	}
	 */

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

    public function findOneBy($criteria)
    {
        $where = '1=1';
        foreach($criteria as $key => $value) $where .= " and `$key`='$value'";

        $row = $this->pdo()->query('select * from `' . $this->table . 
                '` where ' . $where . ' limit 0,1')->fetch(PDO::FETCH_ASSOC);

        if($row) $this->initContent($row);

        return $this; 
    }

	/**
	 * find multi rows from sql db
	 * @return array
	 */
	public function fetchAll( $where , $order = '' , $limit = '' )
	{
		return $this->pdo()->query( 'select * from `' . $this->table . '` where ' . $where . ' ' . $order . ' ' . $limit )->fetchAll( PDO::FETCH_ASSOC );
	}

	public function count( $where = '1=1' )
	{
		$row = $this->pdo()->query( 'select count(*) c from `' . $this->table . '` where ' . $where )->fetch( PDO::FETCH_ASSOC );
        return $row['c'];
	}

	public function isExist( $column , $value )
	{
		return $this->count( "`{$column}`='{$value}'" );
	}
}