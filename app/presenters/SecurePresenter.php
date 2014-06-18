<?php

namespace App\Presenters;

use Nette,
	App\Model;

class SecurePresenter extends BasePresenter
{
    private $acl=null;

    public function startup()
    {        
        parent::startup();
        
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

    }

}