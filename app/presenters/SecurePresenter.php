<?php

namespace App\Presenters;

class SecurePresenter extends BasePresenter
{

    public function startup()
    {
        parent::startup();
        
        if (!$this->user->isLoggedIn()) {
            $this->flashMessage('You have to sign in to access this page.', 'warning');
            $this->redirect('sign:in');
        }

    }

}