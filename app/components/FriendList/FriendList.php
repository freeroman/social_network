<?php

class FriendList extends \Nette\Application\UI\Control
{
    private $service;
    
    private $id;
    
    private $event;
    
    /** @persistent */
    public $keyword;
            
    public function __construct($service, $id, $event = false)
    {
        $this->service = $service;
        $this->id = $id;
        $this->event = $event;
    }
    
    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/list.latte');
        
        if($this->event){
            $template->employees = $this->service->getEmployeesByKeywordWithEvent($this->keyword,$this->id);            
        } else {
            $template->employees = $this->service->getEmployeesByKeywordWithGroup($this->keyword,$this->id);
        }
        
        $template->render();
    }
    
    public function handleDetail($id)
    {        
        $this->presenter->redirect('Homepage:detail', $id);
    }
    
    public function handleAssign($id)
    {
        $this->presenter->handleAssign($id);
        
        $this->redrawControl();
    }
    
    protected function createComponentSearchEmp()
    {
        $form = new Nette\Application\UI\Form();

        $form->setMethod('GET');
        $form->addText('keyword')
                ->setAttribute('class', 'form-control')
                ->setAttribute('placeholder', 'Search')
                ->setValue($this->keyword);
        $form->addSubmit('send', 'Search')
                ->setAttribute('class', 'ajax btn btn-default');


        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = null;
        $renderer->wrappers['pair']['container'] = null;
        $renderer->wrappers['pair']['.error'] = 'has-error';
        $renderer->wrappers['control']['container'] = 'div class=form-group';
        //$renderer->wrappers['label']['container'] = 'small';
        $renderer->wrappers['control']['description'] = 'span class=help-block';
        $renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';

        // make form and controls compatible with Twitter Bootstrap
        $form->getElementPrototype()->class('navbar-form ');

        $form->onSuccess[] =  callback($this, 'processSearchEmp');

        return $form;
    }
        
    public function processSearchEmp($form)
    {
        $values = $form->getValues();
        $this->keyword = $values['keyword'];
        $this->redrawControl();
    }
}
