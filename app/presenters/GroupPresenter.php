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
    
    public function actionEdit($id) {
        $group = $this->context->employees->getGroupById($id);
        if($group->id_employees==$this->getUser()->getId() || $this->getUser()->isInRole('administrator')){
            $form = $this['groupForm'];

            $form->setDefaults($group);
            //$form->setDefaults(array('birth_dt'=> $employee->birth_dt->format('Y-m-d')));

            if (!$group) { // kontrola existence záznamu
                throw new BadRequestException;
            }
            $form->addHidden('id_groups', $id);
            $form->addHidden('id_employees', $group->id_employees);

            $form['send']->caption = 'Save changes';
            $this->template->form = $form;
        } else {
            $this->flashMessage('You are not an administrator of this group', 'warning');
            $this->redirect('Group:detail', $id);
        }
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
        $group = $this->context->employees->getGroupById($this->id_groups);
        if($group->id_employees==$this->user->getId() || $this->getUser()->isInRole('administrator')){
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
    
    protected function createComponentGroupForm()
    {
        $form = new Nette\Application\UI\Form();
        $form->addText('name', 'Name*')
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

        $form->onSuccess[] =  callback($this, 'processGroupForm');
        return $form;
    }
    
    public function processGroupForm($form) {
        $values = $form->getValues();
        $group = array(
            'name' => $values['name']
        );
        if(array_key_exists('id_groups', $values)){
            $group['id_groups']=$values['id_groups'];
            $group['id_employees']=$values['id_employees'];
            $this->context->employees->editGroup($group);
            $id = $group['id_groups'];
        } else {
            $group['id_walls']=$this->context->employees->newWall();
            $group['id_employees']=$this->user->getId();
            $id = $this->context->employees->insertGroup($group);
        }
        $this->redirect('Group:detail', $id);
    }
}