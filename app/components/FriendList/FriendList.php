<?php

class FriendList extends \Nette\Application\UI\Control
{
        
    /** @var TicketService */
    private $employeeService;
    
    private $perPage = 12;
    private $offset = 0;    
            
    public function __construct(EmployeeService $service)
    {
        $this->employeeService = $service;
    }
    
    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/list.latte');
        
        /*
        $count = $this->employeeService->findTicketsCount();
        $template->perPage = $this->perPage;
        $template->showPaging = ($count > $this->perPage) ? true : false;
        $template->showPrev = ($this->offset > 0) ? true : false;
        $template->showNext = ($this->perPage * $this->offset + 1 < $count) ? true : false;
        $template->offset = $this->offset;*/
        
        $template->friends = $this->employeeService->getFriendsById($this->presenter->getUser()->id);

        $template->render();
    }        
    
    /*
    public function handlePage($page)
    {
        $this->offset = $page;
        $this->invalidateControl();
    }*/
    
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
        
        $this->invalidateControl();
    }
    
}
