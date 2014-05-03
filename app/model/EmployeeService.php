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
    
    public function getEmployeesByKeyword($keyword, $id) {
        //return $this->database->select('*')->from(CST::TABLE_EMPLOYEES)->where('first_name LIKE %~like~', $keyword)->fetchAll();
        return $this->database->query('SELECT * FROM employees e
            LEFT JOIN relationships r ON
            r.id_employees1 = ',$id,' OR r.id_employees2 = ',$id,'
            WHERE id_relationships is null AND (first_name LIKE %~like~', $keyword, ' OR surname LIKE %~like~', $keyword,')'
                . 'ORDER BY created_dt')->fetchAll();
    }
    
    public function getFriendsByKeyword($keyword, $id) {
        return $this->database->query('SELECT * FROM employees e
            LEFT JOIN (
            SELECT * FROM relationships
            WHERE
                id_employees1 =',$id,'
                OR id_employees2 =',$id,'
            ) r
            ON r.id_employees2 = e.id_employees
            OR r.id_employees1 = e.id_employees
            WHERE e.id_employees!=', $id)->fetchAll();
    }
    
    public function addFriend($data) {
        $this->database->insert(CST::TABLE_RELATIONSHIPS, $data)->execute();
    }
    
    public function getPendingFriendshipRequests($id) {
        return $this->database->query('SELECT * FROM relationships
            INNER JOIN employees ON id_employees=id_employees1
            WHERE id_employees2=',$id,' AND accepted=0')->fetchAll();
    }
}

