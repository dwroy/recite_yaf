<?php

class BaseModel 
{
	/**
	 * singleton object
	 * @var array
	 */
	private static $pool;

	/**
	 * mysql database connection
	 */
	private $pdo;

	/**
	 * redis database connection 
	 * @var Redis
	 */
	private $cache;

	/**
	 * data name used as table name or cache key prefix
	 * @var string
	 */
	private $name;

    /**
	 * get single object of a data class
	 * @param string $className
	 * @return Model 
	 */
	public static function getInstance( $name )
	{
		if( empty( self::$pool[ $name ] ) )
		{
            $class = $name . 'Model';
			self::$pool[ $name ] = new $class;
		}

		return self::$pool[ $name ];
	}

    public function __construct($name) 
    {
        $this->name = $name;
    }

    public function __get($name)
    {
        return $this->{'get'.$name}();
    }

    public function __set($name, $value)
    {
        $this->{'set'.$name}($value);
    }

    protected function name()
    {
        return $this->name;
    }

    protected function cache()
    {
        return $this->cache;
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
		return $this->name . '-' . $uniqueId;
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

        $this->pdo()->exec( 'insert into `' . $this->name . '` (`' . implode( '`,`' , $columns ) . '`) values ("' . implode( '","' , $values ) . '")' );
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

		return $this->pdo()->exec( 'update `' . $this->name . '` set ' . substr( $tmp , 0 , -1 ) . ' where ' . $where);
	}

	protected function delete( $where , $limit = '0,1' )
	{
		return $this->pdo()->exec( 'delete * from `' . $this->name . '` where ' . $where . ' ' . $limit );
	}

	/**
	 * get data from sql db
	 * @return array
	 */
	public function fetch( $where )
	{
        return $this->pdo()->query( 'select * from `' . $this->name . '` where ' . $where . ' limit 0,1' )->fetch( PDO::FETCH_ASSOC );
	}

	/**
	 * find multi rows from sql db
	 * @return array
	 */
	public function fetchAll( $where , $order = '' , $limit = '' )
	{
		return $this->pdo()->query( 'select * from `' . $this->name . '` where ' . $where . ' ' . $order . ' ' . $limit )->fetchAll( PDO::FETCH_ASSOC );
	}

	public function count( $where = '1=1' )
	{
		$row = $this->pdo()->query( 'select count(*) c from `' . $this->name . '` where ' . $where )->fetch( PDO::FETCH_ASSOC );
        return $row['c'];
	}

	public function isExist( $column , $value )
	{
		return $this->count( "`{$column}`='{$value}'" );
	}
}