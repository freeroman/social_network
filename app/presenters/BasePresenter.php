<?php

namespace App\Presenters;

use Nette,
	App\Model;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{    
    protected function startup() {
        $this->template->userLoggedIn = $this->user->isLoggedIn();
        parent::startup();
    }
}
