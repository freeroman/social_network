<?php

class FriendList extends \Nette\Application\UI\Control
{
        
    /** @var TicketService */
    private $employeeService;
    
    /** @persistent */
    public $keyword;
            
    public function __construct(EmployeeService $service)
    {
        $this->employeeService = $service;
    }
    
    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/list.latte');
        
        $template->friends = $this->employeeService->getEmployeesByKeywordWithGroup($this->keyword,$this->presenter->id_groups);

        $template->render();
    }
    
    public function handleDetail($id)
    {        
        $this->presenter->redirect('Homepage:default', $id);
    }
    
    public function handleAssign($id)
    {        
        $data = array(
            'id_employees' => $id,
            'id_groups' => $this->presenter->id_groups
        );
        
        $this->employeeService->addToGroup($data);
        
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
