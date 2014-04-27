<?php

class EmployeeService
{
    /** @var DibiConnection */  
    private $database;
    
    public function __construct($connection)
    {
        $this->database = $connection;
    }
    /*
    public function insertEmployee($data)
    {
        $this->database->insert(CST::TABLE_EMPLOYEES, $data);
    }*/
    
    public function loginExists($login){
        return $this->database->select('login')->from(CST::TABLE_EMPLOYEES)->where('login=%s', $login)->fetch();
    }
    
    public function newWall() {
        return $this->database->insert(CST::TABLE_WALLS, array('id_sharing_types'=>1))->execute(dibi::IDENTIFIER);        
    }
    
    public function getEmployeeById($id) {
        return $this->database->select('*')->from(CST::TABLE_EMPLOYEES)->where('id_employees=', $id)->fetch();
    }
}

