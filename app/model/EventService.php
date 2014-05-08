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
    
    public function getEvents($id) {
        return $this->database->select('*')
                ->from(CST::TABLE_EVENTS)
                ->leftJoin(CST::TABLE_EVENTS_EMPLOYEES)->as('ee')
                ->using('(id_events)')
                ->where('ee.id_employees=%i', $id)
                ->orderBy('starting_dt desc')
                ->fetchAll();
    }
    
    public function getEventsParticipants($id, $which = null) {
        $sql = $this->database->select('*')
                ->from(CST::TABLE_EVENTS_EMPLOYEES)->as('ee')
                ->leftJoin(CST::TABLE_EMPLOYEES)
                ->using('(id_employees)');
        if($which===null){
            $sql->where('id_events=%i',$id,'AND attendance is null');
        } elseif ($which===TRUE) {
            $sql->where('id_events=%i',$id,'AND attendance = 1');
        } elseif ($which===FALSE){
            $sql->where('id_events=%i',$id,'AND attendance = 0');
        }
        return $sql->fetchAll();
    }
    
    public function getEvent($id) {
        return $this->database->select('*')
                ->from(CST::TABLE_EVENTS)
                ->where('id_events=%i', $id)->fetch();
    }
    
    public function getFeedEvents($id){
        return $this->database->select('*')
                ->from(CST::TABLE_EVENTS_EMPLOYEES)->as('ee')
                ->leftJoin(CST::TABLE_EVENTS)->as('ev')
                ->using('(id_events)')
                ->where('ee.id_employees=%i', $id,' AND starting_dt >= NOW()')
                ->orderBy('starting_dt')
                ->fetchAll(NULL, 10);
    }
}