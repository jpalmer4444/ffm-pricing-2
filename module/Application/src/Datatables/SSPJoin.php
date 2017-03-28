<?php

namespace Application\Datatables;

use \PDO;
use Zend\Log\Logger;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SspJoin
 *
 * @author jasonpalmer
 */
class SSPJoin {

    protected $logger;
    protected $andWhere;
    protected $joinStatement;
    protected $joinMap;
    protected $joinStatementUnion;
    protected $joinCountStatement;
    protected $joinCountStatementUnion;
    protected $debug = FALSE;

    public function __contruct(Logger $logger) {
        $this->logger = $logger;
    }

    public function reset() {
        unset($this->andWhere);
        unset($this->joinStatement);
        unset($this->joinCountStatement);
    }

    public function setAndWhere($andWhere) {
        $this->andWhere = $andWhere;
    }
    
    public function setJoinMap($joinMap) {
        $this->joinMap = $joinMap;
    }

    public function setLogger($logger) {
        $this->logger = $logger;
    }

    public function setDebug($debug) {
        $this->debug = $debug;
    }

    public function setJoinStatement($joinStatement) {
        $this->joinStatement = $joinStatement;
    }

    public function setJoinCountStatement($joinCountStatement) {
        $this->joinCountStatement = $joinCountStatement;
    }

    public function setJoinCountStatementUnion($joinCountStatementUnion) {
        $this->joinCountStatementUnion = $joinCountStatementUnion;
    }

    public function setJoinStatementUnion($joinStatementUnion) {
        $this->joinStatementUnion = $joinStatementUnion;
    }

    private function data_output($columns, $data) {
        $out = array();
        for ($i = 0, $ien = count($data); $i < $ien; $i++) {
            $row = array();
            for ($j = 0, $jen = count($columns); $j < $jen; $j++) {
                $column = $columns[$j];
                // Is there a formatter?
                if (isset($column['formatter'])) {
                    $row[$column['dt']] = $column['formatter']($data[$i][$column['db']], $data[$i]);
                } else {
                    $row[$column['dt']] = $data[$i][$columns[$j]['db']];
                }
            }
            $out[] = $row;
        }
        return $out;
    }

    /**
     * Database connection
     *
     * Obtain an PHP PDO connection from a connection details array
     *
     *  @param  array $conn SQL connection details. The array should have
     *    the following properties
     *     * host - host name
     *     * db   - database name
     *     * user - user name
     *     * pass - user password
     *  @return resource PDO connection
     */
    private function db($conn) {
        if (is_array($conn)) {
            return $this->sql_connect($conn);
        }
        return $conn;
    }

    /**
     * Paging
     *
     * Construct the LIMIT clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @return string SQL limit clause
     */
    function limit($request, $columns) {
        $limit = '';
        if (isset($request['start']) && $request['length'] != -1) {
            $limit = "LIMIT " . intval($request['start']) . ", " . intval($request['length']);
        }
        //$this->logger->log(Logger::INFO, "LIMIT: ".$limit);
        return $limit;
    }

    /**
     * Ordering
     *
     * Construct the ORDER BY clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @return string SQL order by clause
     */
    private function order($request, $columns) {
        $order = '';
        if (isset($request['order']) && count($request['order'])) {
            $orderBy = array();
            $dtColumns = $this->pluck($columns, 'dt');
            for ($i = 0, $ien = count($request['order']); $i < $ien; $i++) {
                // Convert the column index into the column data property
                $columnIdx = intval($request['order'][$i]['column']);
                $requestColumn = $request['columns'][$columnIdx];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];
                if ($requestColumn['orderable'] == 'true') {
                    $dir = $request['order'][$i]['dir'] === 'asc' ?
                            'ASC' :
                            'DESC';
                    $orderBy[] = '`' . $column['db'] . '` ' . $dir;
                }
            }
            $order = 'ORDER BY ' . implode(', ', $orderBy);
        }
        //$this->logger->log(Logger::INFO, "ORDER: {$order}");
        return $order;
    }

    /**
     * Searching / Filtering
     *
     * Construct the WHERE clause for server-side processing SQL query.
     *
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here performance on large
     * databases would be very poor
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @param  array $bindings Array of values for PDO bindings, used in the
     *    sql_exec() function
     *  @return string SQL where clause
     */
    private function filter($request, $columns, &$bindings) {
        $globalSearch = array();
        $columnSearch = array();
        $dtColumns = $this->pluck($columns, 'dt');
        if (isset($request['search']) && $request['search']['value'] != '') {
            $str = $request['search']['value'];
            for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];
                if ($requestColumn['searchable'] == 'true') {
                    $binding = $this->bind($bindings, '%' . $str . '%', PDO::PARAM_STR);
                    $globalSearch[] = "`" . $column['db'] . "` LIKE " . $binding;
                }
            }
        }
        // Individual column filtering
        if (isset($request['columns'])) {
            for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];
                $str = $requestColumn['search']['value'];
                if ($requestColumn['searchable'] == 'true' &&
                        $str != '') {
                    $binding = $this->bind($bindings, '%' . $str . '%', PDO::PARAM_STR);
                    $columnSearch[] = "`" . $column['db'] . "` LIKE " . $binding;
                }
            }
        }
        // Combine the filters into a single string
        $where = '';
        if (count($globalSearch)) {
            $where = '(' . implode(' OR ', $globalSearch) . ')';
        }
        if (count($columnSearch)) {
            $where = $where === '' ?
                    implode(' AND ', $columnSearch) :
                    $where . ' AND ' . implode(' AND ', $columnSearch);
        }
        if ($where !== '') {
            $where = 'WHERE ' . $where;
        }
        //$this->logger->log(Logger::INFO, "FILTER: {$where}");
        return $where;
    }

    /**
     * Perform the SQL queries needed for an server-side processing requested,
     * utilising the helper functions of this class, limit(), order() and
     * filter() among others. The returned array is ready to be encoded as JSON
     * in response to an SSP request, or can be modified if needed before
     * sending back to the client.
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array|PDO $conn PDO connection resource or connection parameters array
     *  @param  string $table SQL table to query
     *  @param  string $primaryKey Primary key of the table
     *  @param  array $columns Column information array
     *  @return array          Server-side processing response array
     */
    public function simple($request, $conn, $table, $primaryKey, $columns) {
        $bindings = array();
        $db = $this->db($conn);
        // Build the SQL query string from the request
        $limit = $this->limit($request, $columns);
        $order = $this->order($request, $columns);
        $where = $this->filter($request, $columns, $bindings);

        //if ($this->debug) {

            //$b4SQL = "SELECT `" . implode("`, `", $this->pluck($columns, 'db')) . "` FROM `$table` $where $order $limit";
            //$this->logger->log(Logger::INFO, PHP_EOL . $b4SQL);
        //}

        if (!empty($this->andWhere)) {
            //only add the and when there is some "where" clause!
            //$where = $this->andWhere . (!empty($where) ? ' AND (' . $where . ')' : '');
            $indexOfWhere = stripos($where, 'where');
            //move to the end of the WHERE clause and wrap in parenthesis.
            $indexOfWhere += 6;
            //substr_replace($oldstr, $str_to_insert, $pos, 0)
            if (!empty($where)) {
                $where = substr_replace($where, '(', $indexOfWhere, 0);
                $where .= (!empty($where) ? ') AND (' : '') . $this->andWhere . ')';
            } else {
                $where .= 'WHERE ' . $this->andWhere;
            }
        }

        if (empty($this->joinStatement)) {
            // Main query to actually get the data
            
            $data = $this->sql_exec($db, $bindings, "SELECT `" . implode("`, `", $this->pluck($columns, 'db')) . "` FROM `$table` $where $order $limit"
            );
        } else {
            $afterJoin = $this->joinStatement . $where . ' ' . $order . ' ' . $limit;
            if ($this->debug) {
                $this->logger->log(Logger::INFO, $afterJoin);
            }
            // Main query to actually get the data
            $dataJoin = $this->sql_exec($db, $bindings, $afterJoin);

            if (!empty($this->joinStatementUnion)) {
                $dataJoinUnion = $this->sql_exec($db, $bindings, $this->joinStatementUnion . $where . ' ' . $order . ' ' . $limit);
                if ($this->debug) {
                    $this->logger->log(Logger::INFO, PHP_EOL . $afterJoin);
                }
                $dataMerged = array_merge($dataJoin, $dataJoinUnion);
            }
            $data = !empty($dataMerged) ? $dataMerged : $dataJoin;
        }

        //the next 2 count queries need a modified count

        if (empty($this->joinCountStatement)) {
            // Data set length after filtering
            $countQuery = "SELECT COUNT(`{$primaryKey}`) FROM  `$table` $where";
            $this->logger->log(Logger::INFO, $countQuery);
            $resFilterLength = $this->sql_exec($db, $bindings, $countQuery);
        } else {
            // Data set length after filtering

            $resFilterLength = $this->sql_exec($db, $bindings, $this->joinCountStatement . ' ' . ' ' . $where);
            $this->logger->log(Logger::INFO, $this->joinCountStatement . ' ' . ' ' . $where);
            if (!empty($this->joinCountStatementUnion)) {
                $resFilterLength2 = $this->sql_exec($db, $bindings, $this->joinCountStatementUnion . ' ' . ' ' . $where);
                $this->logger->log(Logger::INFO, $this->joinCountStatementUnion . ' ' . ' ' . $where);
            }
        }

        $recordsFiltered = $resFilterLength[0][0];

        if (!empty($resFilterLength2)) {
            $recordsFiltered .= $resFilterLength2[0][0];
        }

        if (empty($this->joinCountStatement)) {
            // Total data set length
            if (empty($this->andWhere)) {
                $resTotalLength = $this->sql_exec($db, "SELECT COUNT(`{$primaryKey}`) FROM `$table`");
                $this->logger->log(Logger::INFO, "SELECT COUNT(`{$primaryKey}`) FROM `$table`");
            } else {

                $resTotalLength = $this->sql_exec($db, "SELECT COUNT(`{$primaryKey}`) FROM `$table` WHERE " . $this->andWhere);
                $this->logger->log(Logger::INFO, "SELECT COUNT(`{$primaryKey}`) FROM `$table` WHERE " . $this->andWhere);
            }
        } else {
            // Total data set length
            if (empty($this->andWhere)) {
                $this->logger->log(Logger::INFO, $this->joinCountStatement);
                $resTotalLength = $this->sql_exec($db, $this->joinCountStatement);
            } else {
                $this->logger->log(Logger::INFO, $this->joinCountStatement . " WHERE " . $this->andWhere);
                $resTotalLength = $this->sql_exec($db, $this->joinCountStatement . " WHERE " . $this->andWhere);
            }
            if ($this->joinCountStatementUnion) {
                if (empty($this->andWhere)) {
                    $resTotalLength2 = $this->sql_exec($db, $this->joinCountStatementUnion);
                    $this->logger->log(Logger::INFO, $this->joinCountStatementUnion);
                } else {
                    $resTotalLength2 = $this->sql_exec($db, $this->joinCountStatementUnion . " WHERE " . $this->andWhere);
                    $this->logger->log(Logger::INFO, $this->joinCountStatementUnion . " WHERE " . $this->andWhere);
                }
            }
        }
        $recordsTotal = $resTotalLength[0][0];
        if (!empty($resTotalLength2)) {
            $recordsTotal .= $resTotalLength2[0][0];
        }

        $draw = isset($request['draw']) ?
                intval($request['draw']) :
                0;
        /*
         * Output
         */
        return array(
            "draw" => $draw,
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => $this->data_output($columns, $data)
        );
    }

    /**
     * Connect to the database
     *
     * @param  array $sql_details SQL server connection details array, with the
     *   properties:
     *     * host - host name
     *     * db   - database name
     *     * user - user name
     *     * pass - user password
     * @return resource Database connection handle
     */
    private function sql_connect($sql_details) {
        try {
            $db = @new PDO(
                    "mysql:host={$sql_details['host']};dbname={$sql_details['db']}", $sql_details['user'], $sql_details['pass'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
        } catch (PDOException $e) {
            $this->fatal(
                    "An error occurred while connecting to the database. " .
                    "The error reported by the server was: " . $e->getMessage()
            );
        }
        return $db;
    }

    /**
     * Execute an SQL query on the database
     *
     * @param  resource $db  Database handler
     * @param  array    $bindings Array of PDO binding values from bind() to be
     *   used for safely escaping strings. Note that this can be given as the
     *   SQL query string if no bindings are required.
     * @param  string   $sql SQL query to execute.
     * @return array         Result from the query (all rows)
     */
    private function sql_exec($db, $bindings, $sql = null) {
        // Argument shifting
        if ($sql === null) {
            $sqlWasNull = TRUE;
            $sql = $bindings;
        }
        
        
        
        if(!empty($this->joinMap) && !$sqlWasNull){
            foreach($this->joinMap as $column=>$replaceColumn){
                //$this->logger->log(Logger::INFO, "SQL: "+$sql);
                $sql = str_replace($column, $replaceColumn, $sql);
            }
        }
        
        $this->logger->log(Logger::INFO, "SQL: ".$sql);
        
        $stmt = $db->prepare($sql);
        //echo $sql;
        // Bind parameters
        if (is_array($bindings)) {
            for ($i = 0, $ien = count($bindings); $i < $ien; $i++) {
                $binding = $bindings[$i];
                $stmt->bindValue($binding['key'], $binding['val'], $binding['type']);
                if ($this->debug) {
                    $this->logger->log(Logger::INFO, PHP_EOL . "Binding Key: " . $binding['key'] . ' Value: ' . $binding['val'] . ' Type: ' . $binding['type']);
                }
            }
        }
        //$this->logger->log(Logger::INFO, "EXECUTING: {$sql}");
        // Execute
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            $this->fatal("An SQL error occurred: " . $e->getMessage());
        }
        // Return all
        return $stmt->fetchAll(PDO::FETCH_BOTH);
    }

    /*     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Internal methods
     */

    /**
     * Throw a fatal error.
     *
     * This writes out an error message in a JSON string which DataTables will
     * see and show to the user in the browser.
     *
     * @param  string $msg Message to send to the client
     */
    private function fatal($msg) {
        echo json_encode(array(
            "error" => $msg
        ));
    }

    /**
     * Create a PDO binding key which can be used for escaping variables safely
     * when executing a query with sql_exec()
     *
     * @param  array &$a    Array of bindings
     * @param  *      $val  Value to bind
     * @param  int    $type PDO field type
     * @return string       Bound key to be used in the SQL where this parameter
     *   would be used.
     */
    private function bind(&$a, $val, $type) {
        $key = ':binding_' . count($a);
        $a[] = array(
            'key' => $key,
            'val' => $val,
            'type' => $type
        );
        return $key;
    }

    /**
     * Pull a particular property from each assoc. array in a numeric array, 
     * returning and array of the property values from each item.
     *
     *  @param  array  $a    Array to get data from
     *  @param  string $prop Property to read
     *  @return array        Array of property values
     */
    private function pluck($a, $prop) {
        $out = array();
        for ($i = 0, $len = count($a); $i < $len; $i++) {
            $out[] = $a[$i][$prop];
        }
        return $out;
    }

    /**
     * Return a string from an array or a string
     *
     * @param  array|string $a Array to join
     * @param  string $join Glue for the concatenation
     * @return string Joined string
     */
    private function _flatten($a, $join = ' AND ') {
        if (!$a) {
            return '';
        } else if ($a && is_array($a)) {
            return implode($join, $a);
        }
        return $a;
    }

    /**
     * 
     * @param array Flat array of POST data sent from datatables
     * @param int $index Index of [search][value] in [columns][$index] array
     * @param string $value Value to set in columns array.
     */
    public function setColumnSearchValue(& $datablesPostArgs, $index, $value) {
        $datablesPostArgs['columns'][$index]['search']['value'] = $value;
    }

    /**
     * 
     * @param array $columns Array to get data from
     * @param string $columnName Column Name for Index lookup
     * @return int Index of Column Name in $column array.
     */
    public function pluckColumnIndex($columns, $columnName) {
        for ($i = 0, $len = count($columns); $i < $len; $i++) {
            $mapping = $columns[$i];
            if (strcmp($mapping['db'], $columnName) == 0) {
                return $mapping['dt'];
            }
        }
    }

}
