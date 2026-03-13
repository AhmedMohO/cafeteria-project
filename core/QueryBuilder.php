<?php

namespace Core;

class QueryBuilder
{
    
protected $joins = [];
protected $offsetVal;

    protected $db;
    protected $table;

    protected $select = "*";
    protected $where = [];
    protected $bindings = [];
    protected $limit;
    protected $orderBy;

    public function __construct($db, $table)
    {
        $this->db = $db;
        $this->table = $table;
    }

    public function select($columns)
    {
        if (is_array($columns)) {
            $this->select = implode(',', $columns);
        } else {
            $this->select = $columns;
        }

        return $this;
    }

    public function where($column, $value)
    {
        $this->where[] = "$column = ?";
        $this->bindings[] = $value;

        return $this;
    }

    public function orderBy($column, $direction = "ASC")
    {
        $this->orderBy = "ORDER BY $column $direction";
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = "LIMIT $limit";
        return $this;
    }

    private function buildWhere()
    {
        if (empty($this->where)) {
            return "";
        }

        return "WHERE " . implode(" AND ", $this->where);
    }

    public function get()
{
    $sql = "SELECT {$this->select} FROM {$this->table} ";

    if (!empty($this->joins)) {
        $sql .= implode(' ', $this->joins) . ' ';
    }

    $sql .= $this->buildWhere() . " ";

    if ($this->orderBy) {
        $sql .= $this->orderBy . " ";
    }

    if ($this->limit) {
        $sql .= $this->limit . " ";
    }

    if (isset($this->offsetVal)) {
        $sql .= $this->offsetVal;
    }

    $stmt = $this->db->prepare($sql);
    $stmt->execute($this->bindings);

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

    public function first()
    {
        $this->limit(1);
        $data = $this->get();

        return $data[0] ?? null;
    }

    public function insert($data)
    {
        $columns = implode(",", array_keys($data));

        $placeholders = implode(",", array_fill(0, count($data), "?"));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute(array_values($data));
    }

    public function update($data)
    {
        $fields = [];
        $updateBindings = [];

        foreach ($data as $column => $value) {
            $fields[] = "$column = ?";
            $updateBindings[] = $value;
        }

        $fields = implode(",", $fields);

        $sql = "UPDATE {$this->table} SET $fields ";

        $sql .= $this->buildWhere();

        $allBindings = array_merge($updateBindings, $this->bindings);

        $stmt = $this->db->prepare($sql);

        return $stmt->execute($allBindings);
    }

    public function delete()
    {
        $sql = "DELETE FROM {$this->table} ";

        $sql .= $this->buildWhere();

        $stmt = $this->db->prepare($sql);

        return $stmt->execute($this->bindings);
    }

    public function count()
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} ";

        $sql .= $this->buildWhere();

        $stmt = $this->db->prepare($sql);
        $stmt->execute($this->bindings);

        return $stmt->fetch()['count'];
    }


    public function join($table, $first, $operator, $second, $type = 'LEFT')
    {
        // Store join — rebuild in get()
        $this->joins[] = "$type JOIN $table ON $first $operator $second";
        return $this;
    }

    public function offset($offset)
    {
        $this->offsetVal = "OFFSET $offset";
        return $this;
    }
}
