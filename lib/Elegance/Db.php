<?php
/**
 * Elegance Database Wrapper © 2012
 * Copyright © 2012 Sasquatch <Joan-Alexander Grigorov>
 *                              http://bgscripts.com
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License v3
 * that is bundled with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @category   Elegance
 * @package    Elegance Database Wrapper
 * @copyright  Copyright (c) 2012 Sasquatch, Elegance Team
 */


/**
 * Database wrapper class built over PDO
 * 
 * Very similar to Zend_Db
 * 
 * @author     Joan-Alexander Grigorov http://bgscripts.com
 * @category   Elegance
 * @package    Elegance Database Wrapper
 * @copyright  Copyright (c) 2012 Sasquatch, Elegance Team
 */
class Elegance_Db
{
    
    /**
     * Symbol used for quoting identifiers
     * 
     * @var string
     */
    const IDENTIFIER_QUOTE_SYMBOL = '`';
    
    /**
     * @var PDO
     */
    protected $_driver;

    /**
     * Sets the PDO instance
     * 
     * @param PDO $driver
     */
    public function __construct(PDO $driver)
    {
        $this->_driver = $driver;
    }

    /**
     * Execute SQL query
     * 
     * @param string $sql
     * @param array $bind
     * @param array $driverOptions
     * @return PDOStatement
     */
    public function query($sql, array $bind = array(), array $driverOptions = array())
    {
        $stmt = $this->_driver->prepare($sql, $driverOptions);
        $stmt->execute($bind);
        return $stmt;
    }
    
    /**
     * Fetches single row
     * 
     * @param string $sql
     * @param array $bind
     * @param integer $fetchStyle
     * @param array $driverOptions
     * @return array|stdClass
     */
    public function fetchRow($sql, array $bind = array(), $fetchStyle = PDO::FETCH_OBJ, array $driverOptions = array())
    {
        $stmt = $this->_driver->prepare($sql, $driverOptions);
        $stmt->execute($bind);
        return $stmt->fetch($fetchStyle);
    }

    /**
     * Fetches rowset
     * 
     * @param string $sql
     * @param array $bind
     * @param integer $fetchStyle
     * @param array $driverOptions
     * @return array
     */
    public function fetchAll($sql, array $bind = array(), $fetchStyle = PDO::FETCH_OBJ, array $driverOptions = array())
    {
        $stmt = $this->_driver->prepare($sql, $driverOptions);
        $stmt->execute($bind);
        return $stmt->fetchAll($fetchStyle);
    }
    
    /**
     * Fetch one column
     * 
     * @param string $sql
     * @param array $bind
     * @param array $driverOptions
     * @return string
     */
    public function fetchOne($sql, array $bind = array(), array $driverOptions = array())
    {
        $stmt = $this->_driver->prepare($sql, $driverOptions);
        $stmt->execute($bind);
        return $stmt->fetchColumn();
    }
    
    /**
     * Fetches result set in array as a pair firstColumn => secondColumn
     * 
     * @param string $sql
     * @param array $bind
     * @param array $driverOptions
     * @return array
     */
    public function fetchPair($sql, array $bind = array(), array $driverOptions = array())
    {
        $stmt = $this->_driver->prepare($sql, $driverOptions);
        $stmt->execute($bind);
        $rows = $stmt->fetchAll(PDO::FETCH_NUM);
        
        $pairs = array();
        
        if (empty($rows)) {
            return array();
        }
        
        foreach ($rows as $row) {
            $pairs[$row[0]] = $row[1];
        }
        
        return $pairs;
    }

    /**
     * Quote an identifier
     *
     * @param  string $value The identifier or expression.
     * @return string        The quoted identifier
     */
    public function quoteIdentifier($value)
    {
        $q = self::IDENTIFIER_QUOTE_SYMBOL;
        return ($q . str_replace("$q", "$q$q", $value) . $q);
    }
    
    /**
     * Insert data into database table
     * 
     * @param string $table
     * @param array $bind
     * @param boolean $isIgnore 
     *     If this option is set to true, the SQL will ignore any errors
     *     from duplicating keys
     * @return string Inserted row's primary key value
     */
    public function insert($table, array $bind, $isIgnore = false)
    {
        // extract and quote col names from the array keys
        $cols = array();
        $vals = array();
        $i = 0;
        foreach ($bind as $col => $val) {
            $cols[] = $this->quoteIdentifier($col);
            $vals[] = '?';
        }

        $ignore = $isIgnore ? 'IGNORE' : '';
        
        // build the statement
        $sql = "INSERT $ignore INTO "
             . $this->quoteIdentifier($table)
             . ' (' . implode(', ', $cols) . ') '
             . 'VALUES (' . implode(', ', $vals) . ')';
        
        $this->_driver->beginTransaction();
        
        $stmt = $this->_driver->prepare($sql);
        $stmt->execute(array_values($bind));
        $id = $this->_driver->lastInsertId();
        
        $this->_driver->commit();
        
        return $id;
    }
    
    /**
     * Update row(s)
     * 
     * @param string $table
     * @param array $bind
     * @param array|string $where
     * @param integer $limit
     * @return integer Updated rows count
     */
    public function update($table, array $bind, $where = '', $limit = 0)
    {
        /**
         * Build "col = ?" pairs for the statement
         */
        $set = array();
        $i = 0;
        foreach ($bind as $col => $val) {
            $val = '?';
            $set[] = $this->quoteIdentifier($col) . ' = ' . $val;
        }
        
        $bind = array_values($bind);
        
        if (is_array($where)) {
            $whereSet = array();
            foreach ($where as $col => $val) {
                $bind[] = $val;
                $whereSet[] = $col;
            }
            $where = implode(' AND ', $whereSet);
        }
        
        if ($limit) {
            $limit = (int) $limit;
        }
        
        // build the statement
        $sql = "UPDATE "
             . $this->quoteIdentifier($table)
             . ' SET ' . implode(', ', $set)
             . (($where) ? " WHERE $where" : '')
             . (($limit) ? " LIMIT $limit" : '');
        
        $this->_driver->beginTransaction();
        
        $stmt = $this->_driver->prepare($sql);
        $stmt->execute($bind);
        $affectedRows = $stmt->rowCount();
        $this->_driver->commit();
        
        return $affectedRows;
    }
    
    /**
     * Delete row(s)
     * 
     * @param string $table
     * @param array|string $where
     * @param integer $limit
     * @return integer Deleted rows count
     */
    public function delete($table, $where = '', $limit = null)
    {        
        $bind = array();
        if (is_array($where)) {
            $whereSet = array();
            foreach ($where as $col => $val) {
                $bind[] = $val;
                $whereSet[] = $col;
            }
            $where = implode(' AND ', $whereSet);
        }
        
        if ($limit) {
            $limit = (int) $limit;
        }
        
        // build the statement
        $sql = "DELETE FROM "
        . $this->quoteIdentifier($table)
        . (($where) ? " WHERE $where" : '')
        . (($limit) ? " LIMIT $limit" : '');
        
        $this->_driver->beginTransaction();
        
        $stmt = $this->_driver->prepare($sql);
        $stmt->execute($bind);
        $affectedRows = $stmt->rowCount();
        $this->_driver->commit();
        
        return $affectedRows;
    }
    
}