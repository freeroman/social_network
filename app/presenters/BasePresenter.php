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
