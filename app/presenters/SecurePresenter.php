<?php

namespace App\Presenters;

use Nette,
	App\Model;

class SecurePresenter extends BasePresenter
{
    private $acl=null;

    public function startup()
    {        
        if (!$this->user->isLoggedIn()) {
            $this->flashMessage('You have to sign in to access this page.', 'warning');
            $this->redirect('sign:in');
        } else {
            $this->acl=$this->context->acl;
            $roles=$this->getUser()->getIdentity()->getRoles();
            $role=array_shift($roles);

            if(!$this->acl->isAllowed($role, strtolower($this->name), $this->action)){
                $this->flashMessage('You do not have permission to do this.', 'warning');
                $this->redirect('Homepage:');
            }                
        }
        $this->template->userLoggedIn = $this->user->isLoggedIn();
        //$this->template->employee = $this->context->employees->getEmployeeById($this->user->getId());
        $requests = $this->context->employees->getPendingFriendshipRequests($this->user->getId());
        $this->template->requests = $requests;
        $this->template->count = count($requests);
        parent::startup();
    }
    
    protected function createComponentSearch()
        {
            $form = new Nette\Application\UI\Form();
            
            $form->setMethod('GET');
            $form->addText('search')
                    ->setAttribute('class', 'form-control')
                    ->setAttribute('placeholder', 'Search');
            $form->addSubmit('send', 'Search')
                    ->setAttribute('class', 'btn btn-default');
            
            
            $renderer = $form->getRenderer();
            $renderer->wrappers['controls']['container'] = null;
            $renderer->wrappers['pair']['container'] = null;
            $renderer->wrappers['pair']['.error'] = 'has-error';
            $renderer->wrappers['control']['container'] = 'div class=form-group';
            //$renderer->wrappers['label']['container'] = 'small';
            $renderer->wrappers['control']['description'] = 'span class=help-block';
            $renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';

            // make form and controls compatible with Twitter Bootstrap
            $form->getElementPrototype()->class('navbar-form navbar-left');
            
            $form->onSuccess[] =  callback($this, 'processSearch');
            
            return $form;
        }
        
    public function processSearch($form)
        {
            $values = $form->getValues();
            
            if($values['search']){
                $this->redirect('Homepage:search', $values['search']);
            } else {
                //$this->redirect('this');
            }
        }
}