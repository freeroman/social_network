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
}