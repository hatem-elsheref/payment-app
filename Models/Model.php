<?php

namespace Models;

use Framework\Application;

class Model{
    private $table = "users";

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

    public function insert($data){
        $data = $this->prepareDataBeforeInsert($data);
        $sql = "INSERT INTO $this->table (" . $data['columns'] . ") VALUES (" . $data['values'] . ")";
        var_dump($this->db()->query($sql));
        return $sql;
    }

    // public abstract function delete();
    // public abstract function update();
    // public abstract function getAll();
    // public abstract function getOne();
}