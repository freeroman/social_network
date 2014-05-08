<?php

class EventService{
    
    private $database;
    
    public function __construct(\DibiConnection $database) {
        $this->database=$database;
    }
    
    public function insertEvent($data) {
        $this->database->insert(CST::TABLE_EVENTS, $data)->execute();
        return $this->database->insertId();
    }
    
    public function getEmployeesByKeywordWithEvent($keyword, $id) {
        return $this->database->query('SELECT *
            FROM employees
            LEFT JOIN
            (
                    SELECT * FROM events_employees WHERE id_events=',$id,'
            ) g USING (id_employees)
            WHERE (first_name LIKE %~like~', $keyword, ' OR surname LIKE %~like~', $keyword,')')->fetchAll();
    }
    
    public function addToEvent($data) {
        $this->database->insert(CST::TABLE_EVENTS_EMPLOYEES, $data)->execute();
    }
}
