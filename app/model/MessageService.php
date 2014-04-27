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
    
}
