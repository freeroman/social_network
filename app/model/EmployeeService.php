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
            ON (NOW() BETWEEN created_dt AND valid_to)
                AND (r.id_employees2 = e.id_employees
                OR r.id_employees1 = e.id_employees)
            WHERE e.id_employees!=', $id, 'AND (first_name LIKE %~like~', $keyword, ' OR surname LIKE %~like~', $keyword,')')->fetchAll(null, 6);
    }
    
    public function getGroupsByKeyword($keyword){
        return $this->database->select('*')->from(CST::TABLE_GROUPS)->where('name LIKE %~like~', $keyword)->fetchAll(null, 6);
        /*
         * $sql = $this->database->select('*')->from(CST::TABLE_GROUPS);
        if ($keyword===NULL)
        {
            return $sql->where('name LIKE %~like~', $keyword)->fetchAll(null, 6);
        } else {
            return $sql->innerJoin(CST::TABLE_GROUPS_EMPLOYEES)->as('ge')
                    ->using('id_groups')
                    ->where('ge.id_employees=%i', $id)->fetchAll();
         */
    }
    
    public function editEmployee($data) {
        $id = $data['id_employees'];
        unset($data['id_employees']);
        $this->database->update(CST::TABLE_EMPLOYEES, $data)->where('id_employees=%i', $id)->execute();
    }
    
    public function getGroupById($id) {
        return $this->database->select('e.id_employees, e.first_name, e.surname, g.*')
                ->from(CST::TABLE_GROUPS)->as('g')
                ->leftJoin(CST::TABLE_EMPLOYEES)->as('e')
                ->using('(id_employees)')
                ->where('id_groups=', $id)->fetch();
    }
    
    public function getGroupsById($id) {
        return $this->database->select('g.*')
                ->from(CST::TABLE_GROUPS_EMPLOYEES)->as('ge')
                ->leftJoin(CST::TABLE_GROUPS)->as('g')
                ->using('(id_groups)')
                ->where('ge.id_employees=%i', $id)
                ->union(
                    $this->database->select('*')
                    ->from(CST::TABLE_GROUPS)
                    ->where('id_employees=%i', $id)
                )->fetchAll(null, 15);
    }
    
    public function addFriend($data) {
        $this->database->insert(CST::TABLE_RELATIONSHIPS, $data)->execute();
    }
    
    public function getPendingFriendshipRequests($id) {
        return $this->database->query('SELECT * FROM relationships
            INNER JOIN employees ON id_employees=id_employees1
            WHERE id_employees2=',$id,' AND accepted=0 AND (%s',date('Y-m-d H-i-s'),'BETWEEN created_dt AND valid_to)')->fetchAll();
    }
    
    public function updateFriendship($data, $id) {
        $this->database->update(CST::TABLE_RELATIONSHIPS, $data)->where('id_relationships=', $id)->execute();
    }
    
    public function getFriendsById($id) {
        return $this->database->query('SELECT * FROM employees
            RIGHT JOIN
            (SELECT id_employees2 id_employees FROM relationships
            WHERE id_employees1=', $id ,' AND (NOW() BETWEEN created_dt AND valid_to) AND accepted=1
            UNION
            SELECT id_employees1 id_employees FROM relationships
            WHERE id_employees2=', $id ,' AND (NOW() BETWEEN created_dt AND valid_to) AND accepted=1) rel
            USING (id_employees)')->fetchAll();
    }
    
    public function addToGroup($data) {
        $this->database->insert(CST::TABLE_GROUPS_EMPLOYEES, $data)->execute();
    }
    
    public function getEmployeesByKeywordWithGroup($keyword, $id) {
        return $this->database->query('SELECT *
            FROM employees
            LEFT JOIN
            (
                    SELECT * FROM groups_employees WHERE id_groups=',$id,'
            ) g USING (id_employees)
            WHERE (first_name LIKE %~like~', $keyword, ' OR surname LIKE %~like~', $keyword,')')->fetchAll();
    }
    
    public function getGroupMembers($id) {
        return $this->database->select('*')
                ->from(CST::TABLE_GROUPS_EMPLOYEES)
                ->leftJoin(CST::TABLE_EMPLOYEES)
                ->using('(id_employees)')
                ->where('id_groups=%i', $id)
                ->fetchAll(null, 15);
    }
    
    public function getGroupsWithNewestMessage($id) {
        /*return $this->database->query('SELECT * FROM groups_employees ge
            LEFT JOIN groups g USING (id_groups)
            LEFT JOIN employees e ON e.id_employees=g.id_employees
            LEFT JOIN messages m ON m.id_walls=g.id_walls
            WHERE ge.id_employees=', $id,'ORDER BY created_dt desc')->fetchAll(null, 1);*/
        return $this->database->query('SELECT * FROM groups_employees ge
            LEFT JOIN groups g USING (id_groups)
            LEFT JOIN employees e ON e.id_employees=g.id_employees
            WHERE ge.id_employees=', $id)->fetchAll();
    }
    
    public function insertGroup($data) {
        $this->database->insert(CST::TABLE_GROUPS, $data)->execute();
        return $this->database->getInsertId();
    }
}

