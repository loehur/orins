<?php
require_once 'app/Config/DB_Config.php';

class DB_1 extends DB_Config
{
    private static $_instance = null;
    private $mysqli;
    public $db_pass;

    public function __construct()
    {
        $this->db_pass =  $_SESSION['secure']['db_pass'];
        $this->mysqli = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name) or die('DB Error');
    }

    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new DB_1();
        }

        return self::$_instance;
    }

    public function get($table)
    {
        $reply = [];
        $query = "SELECT * FROM $table";
        $result = $this->mysqli->query($query);

        while ($row = $result->fetch_assoc())
            $reply[] = $row;

        return $reply;
    }

    public function get_where($table, $where)
    {
        $reply = [];
        $query = "SELECT * FROM $table WHERE $where";
        $result = $this->mysqli->query($query);

        while ($row = $result->fetch_assoc())
            $reply[] = $row;

        return $reply;
    }

    public function get_cols_where($table, $cols, $where, $row)
    {
        $reply = [];
        $query = "SELECT $cols FROM $table WHERE $where";
        $result = $this->mysqli->query($query);
        if ($result) {
            switch ($row) {
                case "0":
                    $reply = $result->fetch_assoc();
                    return $reply;
                    break;
                case "1";
                    while ($data = $result->fetch_assoc())
                        $reply[] = $data;
                    return $reply;
                    break;
            }
        } else {
            return array('query' => $query, 'error' => $this->mysqli->error, 'errno' => $this->mysqli->errno);
        }
    }

    public function get_cols_groubBy($table, $cols, $groupBy)
    {
        $reply = [];
        $query = "SELECT $cols FROM $table GROUP BY $groupBy";
        $result = $this->mysqli->query($query);

        while ($row = $result->fetch_assoc())
            $reply[] = $row;

        return $reply;
    }

    public function get_order($table, $order)
    {
        $reply = [];
        $query = "SELECT * FROM $table ORDER BY $order";
        $result = $this->mysqli->query($query);

        while ($row = $result->fetch_assoc())
            $reply[] = $row;

        return $reply;
    }


    public function get_where_order($table, $where, $order)
    {
        $reply = [];
        $query = "SELECT * FROM $table WHERE $where ORDER BY $order";
        $result = $this->mysqli->query($query);

        while ($row = $result->fetch_assoc())
            $reply[] = $row;

        return $reply;
    }

    public function get_where_row($table, $where)
    {
        $reply = [];
        $query = "SELECT * FROM $table WHERE $where";
        $result = $this->mysqli->query($query);
        $reply = $result->fetch_assoc();
        if ($result) {
            return $reply;
        } else {
            return array('query' => $query, 'info' => $this->mysqli->error);
        }
    }

    public function insert($table, $values)
    {
        $query = "INSERT INTO $table VALUES($values)";
        $run = $this->mysqli->query($query);
        if ($run) {
            return TRUE;
        } else {
            return array('query' => $query, 'info' => $this->mysqli->error);
        }
    }

    public function insertCols($table, $columns, $values)
    {
        $query = "INSERT INTO $table($columns) VALUES($values)";
        $run = $this->mysqli->query($query);
        return array('query' => $query, 'error' => $this->mysqli->error, 'errno' => $this->mysqli->errno);
    }

    public function delete_where($table, $where)
    {
        $query = "DELETE FROM $table WHERE $where";
        $run = $this->mysqli->query($query);
        return array('query' => $query, 'error' => $this->mysqli->error, 'errno' => $this->mysqli->errno);
    }

    public function update($table, $set, $where)
    {
        $query = "UPDATE $table SET $set WHERE $where";
        $run = $this->mysqli->query($query);
        return array('query' => $query, 'error' => $this->mysqli->error, 'errno' => $this->mysqli->errno);
    }

    public function count($table)
    {
        $query = "SELECT COUNT(*) as count FROM $table";
        $result = $this->mysqli->query($query);

        $reply = $result->fetch_assoc();
        if ($reply) {
            return $reply['count'];
        } else {
            return array('query' => $query, 'info' => $this->mysqli->error);
        }
    }


    public function count_where($table, $where)
    {
        $query = "SELECT COUNT(*) FROM $table WHERE $where";
        $result = $this->mysqli->query($query);

        $reply = $result->fetch_array();
        if ($reply) {
            return $reply[0];
        } else {
            return array('query' => $query, 'info' => $this->mysqli->error);
        }
    }

    public function count_distinct_where($table, $distinct, $where)
    {
        $query =  "SELECT COUNT(DISTINCT $distinct) as count FROM $table WHERE $where";
        $result = $this->mysqli->query($query);

        $reply = $result->fetch_array();
        if ($reply) {
            return $reply['count'];
        } else {
            return array('query' => $query, 'info' => $this->mysqli->error);
        }
    }

    public function query($query)
    {
        $runQuery = $this->mysqli->query($query);
        if ($runQuery) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function innerJoin1($table, $tb_join, $join_where)
    {
        $query = "SELECT * FROM $table INNER JOIN $tb_join ON $join_where";
        $result = $this->mysqli->query($query);
        if ($result) {
            $reply = [];
            while ($row = $result->fetch_assoc())
                $reply[] = $row;
            return $reply;
        } else {
            return FALSE;
        }
    }

    public function innerJoin2($table, $tb_join1, $join_where1, $tb_join2, $join_where2)
    {
        $query = "SELECT * FROM $table INNER JOIN $tb_join1 ON $join_where1 INNER JOIN $tb_join2 ON $join_where2";
        $result = $this->mysqli->query($query);
        if ($result) {
            $reply = [];
            while ($row = $result->fetch_assoc())
                $reply[] = $row;
            return $reply;
        } else {
            return FALSE;
        }
    }

    public function innerJoin2_where($table, $tb_join1, $join_where1, $tb_join2, $join_where2, $where)
    {
        $query = "SELECT * FROM $table INNER JOIN $tb_join1 ON $join_where1 INNER JOIN $tb_join2 ON $join_where2 WHERE $where";
        $result = $this->mysqli->query($query);
        if ($result) {
            $reply = [];
            while ($row = $result->fetch_assoc())
                $reply[] = $row;
            return $reply;
        } else {
            return FALSE;
        }
    }

    public function innerJoin1_where($table, $tb_join, $join_where, $where)
    {
        $query = "SELECT * FROM $table INNER JOIN $tb_join ON $join_where WHERE $where";
        $result = $this->mysqli->query($query);
        if ($result) {
            $reply = [];
            while ($row = $result->fetch_assoc())
                $reply[] = $row;
            return $reply;
        } else {
            return FALSE;
        }
    }
    public function innerJoin1_orderBy($table, $tb_join, $join_where, $orderBy)
    {
        $query = "SELECT * FROM $table INNER JOIN $tb_join ON $join_where ORDER BY $orderBy";
        $result = $this->mysqli->query($query);
        if ($result) {
            $reply = [];
            while ($row = $result->fetch_assoc())
                $reply[] = $row;
            return $reply;
        } else {
            return FALSE;
        }
    }

    //============================================

    public function sum_col_where($table, $col, $where)
    {
        $query = "SELECT SUM($col) as Total FROM $table WHERE $where";
        $result = $this->mysqli->query($query);

        $reply = $result->fetch_assoc();
        if ($result) {
            return $reply["Total"];
        } else {
            return array('query' => $query, 'info' => $this->mysqli->error);
        }
    }
}
