<?php

namespace Core;

class Model
{
    protected $table;
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    protected function query()
    {
        return new QueryBuilder($this->db, $this->table);
    }

    protected function queryTable(string $table): QueryBuilder
    {
        return new QueryBuilder($this->db, $table);
    }

    public function all()
    {
        return $this->query()->get();
    }

    public function find($id)
    {
        return $this->query()
            ->where('id', $id)
            ->first();
    }

    public function where($column, $value)
    {
        return $this->query()->where($column, $value);
    }

    public function create($data)
    {
        return $this->query()->insert($data);
    }

    public function updateWhere($column, $value, $data)
    {
        return $this->query()
            ->where($column, $value)
            ->update($data);
    }

    public function deleteWhere($column, $value)
    {
        return $this->query()
            ->where($column, $value)
            ->delete();
    }
}
