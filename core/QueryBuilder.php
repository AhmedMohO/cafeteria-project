<?php

namespace Core;

class QueryBuilder
{
    protected $db;
    protected $table;

    protected $select = "*";
    protected $fromExpr = null;
    protected $joins = [];
    protected $where = [];
    protected $bindings = [];
    protected $limit;
    protected $offsetClause = null;
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

    public function from(string $expression): static
    {
        $this->fromExpr = $expression;
        return $this;
    }

    public function join(string $type, string $table, string $condition): static
    {
        $this->joins[] = strtoupper($type) . " JOIN {$table} ON {$condition}";
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

    public function offset(int $offset): static
    {
        $this->offsetClause = "OFFSET {$offset}";
        return $this;
    }

    public function whereLike(string $column, string $value): static
    {
        $this->where[] = "{$column} LIKE ?";
        $this->bindings[] = '%' . $value . '%';
        return $this;
    }

    public function whereIn(string $column, array $values): static
    {
        if (empty($values)) {
            $this->where[] = '1 = 0';
            return $this;
        }
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->where[] = "{$column} IN ({$placeholders})";
        $this->bindings = array_merge($this->bindings, array_values($values));
        return $this;
    }

    public function whereRaw(string $condition, array $bindings = []): static
    {
        $this->where[] = $condition;
        $this->bindings = array_merge($this->bindings, $bindings);
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
        $from = $this->fromExpr ?? $this->table;
        $sql = "SELECT {$this->select} FROM {$from} ";

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

        if ($this->offsetClause) {
            $sql .= $this->offsetClause;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($this->bindings);

        return $stmt->fetchAll();
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

    public function delete(): int
    {
        $sql = "DELETE FROM {$this->table} ";

        $sql .= $this->buildWhere();

        $stmt = $this->db->prepare($sql);
        $stmt->execute($this->bindings);

        return $stmt->rowCount();
    }

    public function count()
    {
        $from = $this->fromExpr ?? $this->table;
        $sql = "SELECT COUNT(*) as count FROM {$from} ";

        if (!empty($this->joins)) {
            $sql .= implode(' ', $this->joins) . ' ';
        }

        $sql .= $this->buildWhere();

        $stmt = $this->db->prepare($sql);
        $stmt->execute($this->bindings);

        return $stmt->fetch()['count'];
    }
}
