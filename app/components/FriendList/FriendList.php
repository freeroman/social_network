<?php

class FriendList extends \Nette\Application\UI\Control
{
        
    /** @var TicketService */
    private $employeeService;
    
    private $perPage = 2;
    private $offset = 0;
    
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
        
        $friends = $this->employeeService->getEmployeesByKeywordWithGroup($this->keyword,$this->presenter->id_groups);
        $count = count($friends);
        $template->perPage = $this->perPage;
        $template->showPaging = ($count > $this->perPage) ? true : false;
        $template->showPrev = ($this->offset > 0) ? true : false;
        $template->showNext = ($this->perPage * $this->offset + 1 < $count) ? true : false;
        $template->offset = $this->offset;
        $friends = $this->employeeService->getEmployeesByKeywordWithGroup($this->keyword,$this->presenter->id_groups, $this->perPage, $this->offset);
        $template->friends = $friends;

        $template->render();
    }        
    
    
    public function handlePage($page)
    {
        $this->offset = $page;
        $this->invalidateControl();
    }
    
    public function handleDetail($id)
    {        
        $this->presenter->redirect('Homepage:default', $id);
    }
    
    public function handleAssign($id)
    {
        /*
        $ticket = $this->employeeService->findTicketBy(array('code' => $code), true);
        $ticket->id_support = $this->presenter->getUser()->id;*/
        
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

        //$this->redirect('Homepage:search', $values['search']);
    }
}
