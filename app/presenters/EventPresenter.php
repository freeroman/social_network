<?php

namespace App\Presenters;

use Nette,
	App\Model;

class EventPresenter extends SecurePresenter{
    
    //private $detailId;
    private $id_groups=null;
    
    private $wall;
    
    public function actionAdd($id)
    {
        $this->id_groups = $id;
    }
    
    public function renderDefault() {
        $this->template->groups = $this->context->employees->getGroupsWithNewestMessage($this->user->getId());
    }
    
    public function renderDetail($id) {
        $group = $this->context->employees->getGroupById($id, FALSE);
        $this->template->group = $group;
        $this->wall = $group->id_walls;
        $this->template->messages = $this->context->messages->getMessagesByGroupId($id);
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
    
    protected function createComponentNewEvent()
    {
        $form = new Nette\Application\UI\Form();
        $form->addGroup('New event');
        $form->addText('name', 'Name')
                ->setRequired('Name can not be empty')
                ->setAttribute('class', 'form-control');
        $form->addText('place', 'Place')
                ->setAttribute('class', 'form-control');
        $form->addTextArea('description', 'Description')
                ->setAttribute('class', 'form-control');
        $form->setCurrentGroup(NULL);
        $form->getElementPrototype()->class('form-horizontal');
        $form->addSubmit('send', 'Vytvořit')
                ->setAttribute('class', 'btn btn-default');
        
        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = 'div class=col-md-6';
        //$renderer->wrappers['pair']['container'] = 'div class=form-group';
        $renderer->wrappers['pair']['.error'] = 'has-error';
        $renderer->wrappers['control']['container'] = 'div class=form-group';
        //$renderer->wrappers['label']['container'] = 'small';
        $renderer->wrappers['control']['description'] = 'span class=help-block';
        $renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';

        // make form and controls compatible with Twitter Bootstrap

        //$form->onValidate[] = callback($this, 'validateEmployeeForm');

        $form->onSuccess[] =  callback($this, 'processNewEventForm');
        return $form;
    }
    
    public function processNewEventForm($form) {
        $values = $form->getValues();
        $event = array(
            'name' => $values['name'],
            'id_employees' => $this->user->getId(),
            'id_walls' => $this->context->employees->newWall(),
            'place' => !empty($values['place']) ? $values['place'] : null,
            'description' => !empty($values['description']) ? $values['description'] : null,
            'created_dt' => date('Y-m-d H-i-s'),
            'starting_dt' => date('Y-m-d H-i-s')
        );
        $id = $this->context->events->insertEvent($event);
        $this->redirect('Event:detail', $id);
    }
}