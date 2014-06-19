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
    
    public function actionEdit($id) {
        $event = $this->context->events->getEvent($id);
        if($event->id_employees==$this->getUser()->getId() || $this->getUser()->isInRole('administrator')){
            $form = $this['eventForm'];

            $form->setDefaults($event);
            $form->setDefaults(array('starting_dt'=> $event->starting_dt->format('Y-m-d')));
            $form->setDefaults(array('ending_dt'=> $event->ending_dt ? $event->ending_dt->format('Y-m-d') : null));

            if (!$event) { // kontrola existence záznamu
                throw new BadRequestException;
            }
            $form->addHidden('id_events', $id);
            //$form->addHidden('id_employees', $event->id_employees);

            $form['send']->caption = 'Save changes';
            $this->template->form = $form;
        } else {
            $this->flashMessage('You are not an administrator of this event', 'warning');
            $this->redirect('Event:detail', $id);
        }
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
    
    protected function createComponentEventForm()
    {
        $form = new Nette\Application\UI\Form();
        $form->addText('name', 'Name*')
                ->setRequired('Name can not be empty')
                ->setAttribute('class', 'form-control');
        $form->addText('place', 'Place')
                ->setAttribute('class', 'form-control');
        $form->addTextArea('description', 'Description')
                ->setAttribute('class', 'form-control');
        $form->addText('starting_dt', 'Starting*')
                ->setRequired('Event has to have starting time.')
                ->setAttribute('class', 'form-control');
        $form->addText('ending_dt', 'Ending')
                ->setAttribute('class', 'form-control');
        $form->setCurrentGroup(NULL);
        $form->addSubmit('send', 'Vytvořit')
                ->setAttribute('class', 'btn btn-default');
        $form->getElementPrototype()->class('form-horizontal');
        
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

        $form->onSuccess[] =  callback($this, 'processEventForm');
        return $form;
    }
    
    public function processEventForm($form) {
        $values = $form->getValues();
        if(array_key_exists('id_events', $values)){
            $event = array(
                'name' => $values['name'],
                'place' => !empty($values['place']) ? $values['place'] : null,
                'description' => !empty($values['description']) ? $values['description'] : null,
                'starting_dt' => $values['starting_dt'],
                'ending_dt' => !empty($values['ending_dt']) ? $values['ending_dt'] : null,
                'id_events' => $values['id_events']
            );
            $this->context->events->editEvent($event);
            $id = $values['id_events'];
        } else {
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
        }
        $this->redirect('Event:detail', $id);
    }
    
    public function handleAssign($id)
    {
        $administrator = $this->context->events->getEvent($this->id_events)->id_employees;
        if($administrator==$this->user->getId() || $this->getUser()->isInRole('administrator')){
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