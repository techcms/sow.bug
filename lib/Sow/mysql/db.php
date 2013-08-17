<?php namespace  Sow\mysql;
use Sow\bug as Y;
use Sow\sys\Exception as Exception;
class db {

    public $config = array(
        'host'       => '127.0.0.1',
        'port'       => 3306,
        'user'       => 'test',
        'password'   => '',
        'database'   => 'test',
        'charset'    => 'utf8',
        'options'    => array()
    );

    /**
     * Connection
     *
     * @var resource
     */
    public $conn = null;

    /**
     * Query handler
     *
     * @var resource
     */
    public $query = null;

    /**
     * Debug or not
     *
     * @var boolean
     */
    public $debug = false;

    /**
     * Log
     *
     * @var array
     */
    public $log = array();

    /**
     * Constructor.
     *
     * $config is an array of key/value pairs
     * containing configuration options.  These options are common to most adapters:
     *
     * host           => (string) What host to connect to, defaults to localhost
     * user           => (string) Connect to the database as this username.
     * password       => (string) Password associated with the username.
     * database       => (string) The name of the database to user
     *
     * Some options are used on a case-by-case basis by adapters:
     *
     * port           => (string) The port of the database
     * persistent     => (boolean) Whether to use a persistent connection or not, defaults to false
     * charset        => (string) The charset of the database
     *
     * @param array   $config
     */
    public function __construct( $config ) {
        $config = Y::config('mysql')->$config->toArray();
        $this->config = $config + $this->config;
    }

    /**
     * Query sql
     *
     * @param string  $sql
     * @return resource
     */
    public function query( $sql ) {
        if ( is_null( $this->conn ) ) {
            $this->connect();
        }

        $log = $sql . '@' . date( 'Y-m-d H:i:s' );
        if ( $this->debug ) {
            $this->log[] = $log;
        }

        if ( $this->query = $this->_query( $sql ) ) {
            return $this->query;
        }

        $this->log[] = $log;
        $this->_throwException();
    }
    public function ping($reconnect = true)
    {
        if ($this->conn && $this->conn->ping()) {
            return true;
        }

        if ($reconnect) {
            $this->close();
            $this->connect();
            return $this->conn->ping();
        }

        return false;
    }
    public function connect() {
        if ( $this->ping( false ) ) {
            return $this->conn;
        }

        if ( !extension_loaded( 'mysqli' ) ) {
            throw new Exception( 'NO_MYSQLI_EXTENSION_FOUND' );
        }

        $this->conn = mysqli_init();
        $connected = @mysqli_real_connect(
            $this->conn, $this->config['host'], $this->config['user'],
            $this->config['password'], $this->config['database'], $this->config['port']
        );

        if ( $connected ) {
            if ( $this->config['charset'] ) $this->query( "SET NAMES '{$this->config['charset']}';" );
            return $this->conn;
        }

        $this->_throwException();
    }

    /**
     * Get SQL result
     *
     * @param string  $sql
     * @param string  $type
     * @return mixed
     */
    public function sql( $sql, $type = 'ASSOC' ) {
        $this->query( $sql );

        $tags = explode( ' ', $sql, 2 );
        switch ( strtoupper( $tags[0] ) ) {
        case 'SELECT':
            ( $result = $this->fetchAll( $type ) ) || ( $result = array() );
            break;
        case 'INSERT':
            $result = $this->lastInsertId();
            break;
        case 'UPDATE':
        case 'DELETE':
            $result = $this->affectedRows();
            break;
        default:
            $result = $this->query;
        }

        return $result;
    }

    /**
     * Get a result row
     *
     * @param string  $sql
     * @param string  $type
     * @return array
     */
    public function row( $sql, $type = 'ASSOC' ) {
        $this->query( $sql );
        return $this->fetch( $type );
    }

    /**
     * Get first column of result
     *
     * @param string  $sql
     * @return string
     */
    public function col( $sql ) {
        $this->query( $sql );
        $result = $this->fetch();
        return empty( $result ) ? null : current( $result );
    }

    /**
     * Find data
     *
     * @param array   $opts
     * @return array
     */
    public function find( $opts ) {
        if ( is_string( $opts ) ) {
            $opts = array( 'where' => $opts );
        }

        $opts = $opts + array(
            'fileds' => '*',
            'where' => 1,
            'order' => null,
            'start' => -1,
            'limit' => -1
        );

        $sql = "select {$opts['fileds']} from {$opts['table']} where {$opts['where']}";

        if ( $opts['order'] ) {
            $sql .= " order by {$opts['order']}";
        }

        if ( 0 <= $opts['start'] && 0 <= $opts['limit'] ) {
            $sql .= " limit {$opts['start']}, {$opts['limit']}";
        }

        return $this->sql( $sql );
    }

    /**
     * Insert
     *
     * @param array   $data
     * @param string  $table
     * @return boolean
     */
    public function insert( $data, $table ) {
        $keys = array();
        $values = array();
        foreach ( $data as $key => $value ) {
            $keys[] = "`$key`";
            $values[] = "'" . $this->escape( $value ) . "'";
        }
        $keys = implode( ',', $keys );
        $values = implode( ',', $values );
        $sql = "insert into {$table} ({$keys}) values ({$values});";
        return $this->sql( $sql );
    }

    /**
     * Update table
     *
     * @param array   $data
     * @param string  $where
     * @param string  $table
     * @return int
     */
    public function update( $data, $where = '0', $table ) {
        $tmp = array();

        foreach ( $data as $key => $value ) {
            $tmp[] = "`$key`='" . $this->escape( $value ) . "'";
        }

        $str = implode( ',', $tmp );

        $sql = "update {$table} set {$str} where {$where}";

        return $this->sql( $sql );
    }

    /**
     * Delete from table
     *
     * @param string  $where
     * @param string  $table
     * @return int
     */
    public function delete( $where = '0', $table ) {
        $sql = "delete from $table where $where";
        return $this->sql( $sql );
    }

    /**
     * Count num rows
     *
     * @param string  $where
     * @param string  $table
     * @return int
     */
    public function count( $where, $table ) {
        $sql = "select count(1) as cnt from $table where $where";
        $this->query( $sql );
        $result = $this->fetch();
        return empty( $result['cnt'] ) ? 0 : $result['cnt'];
    }

    /**
     * Throw error exception
     *
     */
    protected function _throwException() {
        $error = $this->error();
        throw new Exception( $error['msg'], $error['code'] );
    }




    /**
     * Select Database
     *
     * @param string  $database
     * @return boolean
     */
    public function selectDb( $database ) {
        return $this->conn->select_db( $database );
    }

    /**
     * Close db connection
     *
     */
    public function close() {
        if ( $this->conn ) {
            return $this->conn->close();
        }

        return true;
    }

    /**
     * Free query result
     *
     */
    public function free() {
        if ( $this->query ) {
            return $this->query->free();
        }
    }

    /**
     * Query SQL
     *
     * @param string  $sql
     * @return
     */
    protected function _query( $sql ) {
        return $this->conn->query( $sql );
    }

    public function execute( $sql ) {
        return $this->conn->query( $sql );
    }


    /**
     * Return the rows affected of the last sql
     *
     * @return int
     */
    public function affectedRows() {
        return $this->conn->affected_rows;
    }

    /**
     * Fetch result
     *
     * @param string  $type
     * @return mixed
     */
    public function fetch( $type = 'ASSOC' ) {
        switch ( $type ) {
        case 'ASSOC':
            $func = 'fetch_assoc';
            break;
        case 'BOTH':
            $func = 'fetch_array';
            break;
        case 'OBJECT':
            $func = 'fetch_object';
            break;
        default:
            $func = 'fetch_assoc';
        }

        return $this->query->$func();
    }

    /**
     * Fetch all results
     *
     * @param string  $type
     * @return mixed
     */
    public function fetchAll( $type = 'ASSOC' ) {
        switch ( $type ) {
        case 'ASSOC':
            $func = 'fetch_assoc';
            break;
        case 'BOTH':
            $func = 'fetch_array';
            break;
        case 'OBJECT':
            $func = 'fetch_object';
            break;
        default:
            $func = 'fetch_assoc';
        }

        $result = array();
        while ( $row = $this->query->$func() ) {
            $result[] = $row;
        }
        $this->query->free();
        return $result;


    }

    /**
     * Get last insert id
     *
     * @return mixed
     */
    public function lastInsertId() {
        return $this->conn->insert_id;
    }

    /**
     * Begin transaction
     *
     */
    public function beginTransaction() {
        return $this->conn->autocommit( false );
    }

    /**
     * Commit transaction
     *
     */
    public function commit() {
        $this->conn->commit();
        $this->conn->autocommit( true );
    }

    /**
     * Rollback
     *
     */
    public function rollBack() {
        $this->conn->rollback();
        $this->conn->autocommit( true );
    }

    /**
     * Escape string
     *
     * @param string  $str
     * @return string
     */
    public function escape( $str ) {
        if ( $this->conn ) {
            return  $this->conn->real_escape_string( $str );
        }else {
            return addslashes( $str );
        }
    }

    /**
     * Get error
     *
     * @return array
     */
    public function error() {
        if ( $this->conn ) {
            $errno = $this->conn->errno;
            $error = $this->conn->error;
        } else {
            $errno = mysqli_connect_errno();
            $error = mysqli_connect_error();
        }

        return array( 'code' => intval( $errno ), 'msg' => $error );
    }


}
