<?php

namespace Models;

use Framework\Application;

class Model{
    protected $table = "users";



    private function db()
    {
        return Application::$app->database::$connectionHandler;
    }

    public function prepareDataBeforeInsert($data)
    {
        $columns = implode(',', array_keys($data));
        $values  = implode(',', array_map(function ($column) {
            return ":$column";
        }, array_keys($data)));

        return ['columns' => $columns, 'values' => $values];
    }


    public function prepareDataBeforeUpdate($data)
    {
        $values  = implode(',', array_map(function ($column) {
            return "$column=:$column";
        }, array_keys($data)));

        return $values;
    }


    public function insert($data){
        $preparedData = $this->prepareDataBeforeInsert($data);
        $sql = "INSERT INTO $this->table (" . $preparedData['columns'] . ") VALUES (" . $preparedData['values'] . ")";
        $stmt = $this->db()->prepare($sql);
        $data = array_combine(explode(',', $preparedData['values']), array_values($data));
        $result = $stmt->execute($data);
        if ($result === false)
            return false;
        else
            return $this->db()->lastInsertId();
    }

    public function update($data, $id, $columnId = 'id'){
        $preparedData = $this->prepareDataBeforeUpdate($data);
        $sql = "UPDATE $this->table SET $preparedData WHERE $columnId = :id";
        $stmt = $this->db()->prepare($sql);
        $keys = array_map(fn($column) => ":$column",array_keys($data));
        $data = array_combine($keys, array_values($data));
        $data[":$columnId"] = $id;
        return $stmt->execute($data);
    }


    public function getAll($select = "*", $conditions = [])
    {
        $sql = "SELECT " . (is_array($select) ? implode(",", $select) : $select) . " FROM $this->table ";

        $data = [];
        if (!empty($conditions) && is_array($conditions)){
            $conditionQuery = " WHERE ";
            foreach ($conditions as $index => $condition) {
                $conditionQuery.= " $condition[0] $condition[1] :$condition[0] AND";
                $data[":$condition[0]"] = $condition[2];
            }
            $sql = $sql . trim($conditionQuery, "AND");
        }

        $stmt = $this->db()->prepare($sql);
        $stmt->execute($data);
        return $stmt->fetchAll();
    }



    public function find($id, $column = 'id', $conditions = [])
    {
        $sql = "SELECT * FROM $this->table WHERE $column = :$column ";

        $data = [];
        if (!empty($conditions) && is_array($conditions)){
            $conditionQuery = " AND ";
            foreach ($conditions as $index => $condition) {
                $conditionQuery.= " $condition[0] $condition[1] :$condition[0] AND";
                $data[":$condition[0]"] = $condition[2];
            }
            $sql = $sql . trim($conditionQuery, "AND") . " LIMIT 1";
        }

        $stmt = $this->db()->prepare($sql);
        $stmt->execute([":$column" => $id, ...$data]);
        return $stmt->fetch();
    }
    // public abstract function delete();
    // public abstract function update();
    // public abstract function getAll();
    // public abstract function getOne();
}