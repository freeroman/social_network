<?php

namespace App\Presenters;

use Nette,
	App\Model;

class EventPresenter extends SecurePresenter{
    
    //private $detailId;
    private $id_events=null;
    
    private $wall = null;
    
    public function actionDetail($id)
    {
        $this->id_events = $id;
    }
    
    public function renderDefault() {
        $this->template->events = $this->context->events->getEvents($this->user->getId());
    }
    
    public function renderDetail($id) {
        $event = $this->context->events->getEvent($id);
        $this->template->event = $event;
        
        $this->wall = $event->id_walls;
        $this->template->messages = $this->context->messages->getMessagesByWall($event->id_walls);
        $this->template->going = $this->context->events->getEventsParticipants($id, TRUE);
        $this->template->notGoing = $this->context->events->getEventsParticipants($id, FALSE);
        $this->template->invited = $this->context->events->getEventsParticipants($id);
    }
    
    protected function createComponentFriendList()
    {
        $list = new \FriendList($this->context->events, $this->id_events, TRUE);
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
        $form->addText('name', 'Name*')
                ->setRequired('Name can not be empty')
                ->setAttribute('class', 'form-control');
        $form->addText('place', 'Place')
                ->setAttribute('class', 'form-control');
        $form->addTextArea('description', 'Description')
                ->setAttribute('class', 'form-control');
        $form->addText('starting_dt', 'Starting')
                ->setAttribute('class', 'form-control');
        $form->addText('ending_dt', 'Ending')
                ->setAttribute('class', 'form-control');
        $form->setCurrentGroup(NULL);
        $form->addSubmit('send', 'Vytvořit')
                ->setAttribute('class', 'btn btn-default');
        $form->getElementPrototype()->class('form-horizontal');
        
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
            'starting_dt' => $values['starting_dt'],
            'ending_dt' => !empty($values['ending_dt']) ? $values['ending_dt'] : null
        );
        $id = $this->context->events->insertEvent($event);
        $this->redirect('Event:detail', $id);
    }
    
    public function handleAssign($id)
    {
        $administrator = $this->context->events->getEvent($this->id_events)->id_employees;
        if($administrator==$this->user->getId()){
            $data = array(
                'id_employees' => $id,
                'id_events' => $this->id_events,
                'created_dt' => date('Y-m-d H-i-s')
            );
            $this->context->events->addToEvent($data);
        } else {
            $this->flashMessage('You are not an administrator of this event', 'warning');
            $this->redirect('Event:detail', $this->id_events);
        }
    }
}