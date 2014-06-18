<?php

class MessageService{
    
    private $db;
    
    public function __construct(\DibiConnection $database) {
        $this->db=$database;
    }
    
    public function getMessagesByUserId($id) {
        //return $this->db->select('text, created_dt')->from(CST::TABLE_MESSAGES)->where('id_walls=', $id)->fetchAll();
        return $this->db->select('m.*, ee.*, f.file, f.type')
                ->from(CST::TABLE_MESSAGES)->as('m')
                ->leftJoin(CST::TABLE_EMPLOYEES)->as('ew')
                ->on('m.id_walls=ew.id_walls')
                ->leftJoin(CST::TABLE_EMPLOYEES)->as('ee')
                ->on('m.id_employees=ee.id_employees')
                ->leftJoin(CST::TABLE_FILES)->as('f')
                ->using('(id_messages)')
                ->where('ew.id_employees=%i',$id, ' AND \''.date('Y-m-d H-i-s').'\' BETWEEN m.visible_from AND m.visible_to')
                ->orderBy('created_dt desc')->fetchAll(null, 10);
        /*
        return $this->db->query('SELECT * '
                . 'FROM ', CST::TABLE_EMPLOYEES, ' '
                . 'INNER JOIN ', CST::TABLE_MESSAGES, ' '
                . 'USING(id_walls) '
                . 'WHERE ', CST::TABLE_EMPLOYEES.'.id_employees=', $id, 'ORDER BY created_dt desc')->fetchAll();*/
    }
    
    public function insertMessage($data) {
        $this->db->insert(CST::TABLE_MESSAGES, $data)->execute();
        return $this->db->getInsertId();
    }
    
    public function deleteMessage($id) {
        $this->db->update(CST::TABLE_MESSAGES, array('visible_to'=>date('Y-m-d H-i-s')))->where('id_messages=%i', $id)->execute();
    }
    
    public function reAssignFile($from, $to) {
        $this->db->query('INSERT INTO files(file, id_employees, id_sharing_types, created_dt, id_albums, type, visible_from, visible_to, id_messages) SELECT file, id_employees, id_sharing_types, created_dt, id_albums, type, visible_from, visible_to, %i',$to,' FROM files WHERE id_messages=%i', $from);
    }
    
    public function insertFile($data) {
        $this->db->insert(CST::TABLE_FILES, $data)->execute();
    }
    
    public function getMessagesByGroupId($id){
        return $this->db->select('m.*, g.*, f.file, f.type, e.*')
                ->from(CST::TABLE_MESSAGES)->as('m')
                ->leftJoin(CST::TABLE_GROUPS)->as('g')
                ->on('g.id_walls=m.id_walls')
                ->leftJoin(CST::TABLE_EMPLOYEES)->as('e')
                ->on('m.id_employees=e.id_employees')
                ->leftJoin(CST::TABLE_FILES)->as('f')
                ->on('m.id_messages=f.id_messages')
                ->where('g.id_groups=%i', $id, ' AND \''.date('Y-m-d H-i-s').'\' BETWEEN m.visible_from AND m.visible_to')
                ->orderBy('m.created_dt desc')
                ->fetchAll();
    }
    
    public function getMessageById($id) {
        return $this->db->select('*')->from(CST::TABLE_MESSAGES)->where('id_messages=%i', $id)->fetch();        
    }
    
    public function getMessagesByWall($id){
        return $this->db->select('*')
                ->from(CST::TABLE_MESSAGES)
                ->leftJoin(CST::TABLE_EMPLOYEES)
                ->using('(id_employees)')
                ->leftJoin(CST::TABLE_FILES)
                ->using('(id_messages)')
                ->where(CST::TABLE_MESSAGES.'.id_walls=%i', $id, ' AND \''.date('Y-m-d H-i-s').'\' BETWEEN visible_from AND visible_to')
                ->fetchAll(null, 10);
    }
    
    public function getFriendsMessages($id) {
        return $this->db->query('SELECT e.*, m.*, f.file, f.type FROM relationships
            LEFT JOIN employees e ON id_employees=id_employees2
            INNER JOIN messages m USING(id_employees)
            LEFT JOIN files f USING (id_messages)
            WHERE id_employees1=',$id,'AND accepted=',1, '
            UNION
            SELECT e.*, m.*, f.file, f.type FROM relationships
            LEFT JOIN employees e ON id_employees=id_employees1
            INNER JOIN messages m USING(id_employees)
            LEFT JOIN files f USING (id_messages)
            WHERE id_employees2=',$id,'AND accepted=',1, ' AND \''.date('Y-m-d H-i-s').'\' BETWEEN m.visible_from AND m.visible_to')
                ->fetchAll(null, 10);
    }
    
}
