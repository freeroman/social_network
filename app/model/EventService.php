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
    
    public function editEvent($data) {
        $id = $data['id_events'];
        unset($data['id_events']);
        $this->database->update(CST::TABLE_EVENTS, $data)->where('id_events=%i', $id)->execute();
    }
    
    public function getEmployeesByKeywordWithEvent($keyword, $id) {
        return $this->database->query('SELECT *
            FROM employees
            LEFT JOIN
            (
                    SELECT * FROM events_employees WHERE id_events=',$id,'
            ) g USING (id_employees)
            WHERE (first_name LIKE %~like~', $keyword, ' OR surname LIKE %~like~', $keyword,')')->fetchAll(null, 10);
    }
    
    public function addToEvent($data) {
        $this->database->insert(CST::TABLE_EVENTS_EMPLOYEES, $data)->execute();
    }
    
    public function getEvents($id) {
        return $this->database->select('DISTINCT e.*')
                ->from(CST::TABLE_EVENTS)->as('e')
                ->leftJoin(CST::TABLE_EVENTS_EMPLOYEES)->as('ee')
                ->using('(id_events)')
                ->where('ee.id_employees=%i', $id, 'OR e.id_employees=%i', $id)
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
        return $this->database->select('ev.*, emp.first_name, emp.surname')
                ->from(CST::TABLE_EVENTS)->as('ev')
                ->leftJoin(CST::TABLE_EMPLOYEES)->as('emp')
                ->using('(id_employees)')
                ->where('id_events=%i', $id)->fetch();
    }
    
    public function getFeedEvents($id){
        return $this->database->select('DISTINCT ev.*')
                ->from(CST::TABLE_EVENTS)->as('ev')
                ->leftJoin(CST::TABLE_EVENTS_EMPLOYEES)->as('ee')
                ->using('(id_events)')
                ->where('(ee.id_employees=%i', $id,'OR ev.id_employees=%i',$id,') AND starting_dt >= NOW()')
                ->orderBy('starting_dt')
                ->fetchAll(NULL, 10);
    }
    
    public function getEventsByKeyword($keyword){
        return $this->database->select('*')->from(CST::TABLE_EVENTS)->where('name LIKE %~like~', $keyword, 'OR place LIKE %~like~', $keyword)->fetchAll(null, 6);
    }
}
