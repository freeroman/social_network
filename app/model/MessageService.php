<?php

class MessageService{
    
    private $db;
    
    public function __construct(\DibiConnection $database) {
        $this->db=$database;
    }
    
    public function getMessagesByUserId($id) {
        //return $this->db->select('text, created_dt')->from(CST::TABLE_MESSAGES)->where('id_walls=', $id)->fetchAll(); 
        return $this->db->query('SELECT * '
                . 'FROM ', CST::TABLE_EMPLOYEES, ' '
                . 'INNER JOIN ', CST::TABLE_MESSAGES, ' '
                . 'USING(id_walls) '
                . 'WHERE ', CST::TABLE_EMPLOYEES.'.id_employees=', $id, 'ORDER BY created_dt desc')->fetchAll();
    }
    
    public function insertMessage($data) {
        $this->db->insert(CST::TABLE_MESSAGES, $data)->execute();
    }
    
    public function getMessagesByGroupId($id){
        return $this->db->select('*')
                ->from(CST::TABLE_MESSAGES)->as('m')
                ->leftJoin(CST::TABLE_GROUPS)->as('g')
                ->on('g.id_walls=m.id_walls')
                ->leftJoin(CST::TABLE_EMPLOYEES)->as('e')
                ->on('m.id_employees=e.id_employees')
                ->where('g.id_groups=%i', $id)
                ->fetchAll();
    }
    
    public function getFriendsMessages($id) {
        return $this->db->query('SELECT e.*, m.* FROM relationships
            LEFT JOIN employees e ON id_employees=id_employees2
            INNER JOIN messages m USING(id_employees)
            WHERE id_employees1=',$id,'
            UNION
            SELECT e.*, m.* FROM relationships
            LEFT JOIN employees e ON id_employees=id_employees1
            INNER JOIN messages m USING(id_employees)
            WHERE id_employees2=',$id,'AND accepted=',1)->fetchAll(null, 10);
    }
    
}
