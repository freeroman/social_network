<?php

class AclModel{
    private $acl=null;

    public function __construct() {
        $this->acl = new Nette\Security\Permission;

        $this->acl->addRole('employee');
        $this->acl->addRole('administrator');

        $this->acl->addResource('homepage');
        $this->acl->addResource('group');
        $this->acl->addResource('sign');
        $this->acl->addResource('event');
        $this->acl->addResource('employee');
        $this->acl->addResource('message');

        $this->acl->allow('employee', 'homepage');
        $this->acl->allow('employee', 'message');
        $this->acl->allow('employee', 'group', Array('default','detail', 'add', 'edit'));
        $this->acl->allow('employee', 'event', Array('default','detail', 'add', 'edit'));
        $this->acl->allow('employee', 'employee', 'editEmployee');
        //$this->acl->allow('admin', 'notice', Array('delete', 'edit'));
        $this->acl->allow('administrator', Nette\Security\Permission::ALL);
    }

    public function isAllowed($role, $resource, $privilage) {
        return $this->acl->isAllowed($role, $resource, $privilage);
    }
}