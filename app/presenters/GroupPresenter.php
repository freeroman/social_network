<?php

namespace App\Presenters;

use Nette,
	App\Model;

class GroupPresenter extends SecurePresenter{
    
    //private $detailId;
    private $id_groups=null;
    
    private $wall = null;
    
    private $group = null;
    
    public function actionDetail($id)
    {
        $this->id_groups = $id;
    }
    
    public function renderDefault() {
        $this->template->groups = $this->context->employees->getGroupsWithNewestMessage($this->user->getId());
    }
    
    public function renderDetail($id) {
        $group = $this->context->employees->getGroupById($id);
        
        $this->group = $group;
        $this->template->group = $group;
        
        $this->wall = $group->id_walls;
        //$this->id_groups = $group->id_groups;
        $this->template->messages = $this->context->messages->getMessagesByGroupId($id);
        $this->template->members = $this->context->employees->getGroupMembers($id);
    }
    
    protected function createComponentFriendList()
    {
        $list = new \FriendList($this->context->employees, $this->id_groups);
        return $list;       
    }
    
    protected function createComponentNewMessage()
    {
        $list = new \NewMessage($this->context->messages, $this->user->getId(), $this->wall);
        return $list;
    }
    
    public function handleAssign($id)
    {
        if($this->group['id_employees']===$this->user->getId()){
            $data = array(
                'id_employees' => $id,
                'id_groups' => $this->id_groups
            );
            $this->context->employees->addToGroup($data);
        } else {
            $this->flashMessage('You are not an administrator of this group', 'warning');
            $this->redirect('Group:detail', $this->id_groups);
        }
    }
    
    protected function createComponentNewGroup()
    {
        $form = new Nette\Application\UI\Form();
        $form->addGroup('New group');
        $form->addText('name', 'Name')
                ->setRequired('Name can not be empty')
                ->setAttribute('class', 'form-control');
        $form->setCurrentGroup(NULL);
              //  ->setOption('container', 'fieldset id=adress');
        //$form->setCurrentGroup(NULL);
        $form->getElementPrototype()->class('form-horizontal');
        $form->addSubmit('send', 'Vytvořit')
                ->setAttribute('class', 'btn btn-default');
        
        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = null;
        //$renderer->wrappers['pair']['container'] = 'div class=form-group';
        $renderer->wrappers['pair']['.error'] = 'has-error';
        $renderer->wrappers['control']['container'] = 'div class=form-group';
        //$renderer->wrappers['label']['container'] = 'small';
        $renderer->wrappers['control']['description'] = 'span class=help-block';
        $renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';

        // make form and controls compatible with Twitter Bootstrap

        //$form->onValidate[] = callback($this, 'validateEmployeeForm');

        $form->onSuccess[] =  callback($this, 'processNewEmployeeForm');
        return $form;
    }
    
    public function processNewEmployeeForm($form) {
        $values = $form->getValues();
        $group = array(
            'name' => $values['name'],
            'id_employees' => $this->user->getId(),
            'id_walls' => $this->context->employees->newWall()
        );
        $id = $this->context->employees->insertGroup($group);
        $this->redirect('Group:detail', $id);
    }
}