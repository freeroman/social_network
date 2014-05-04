<?php

namespace App\Presenters;

use Nette,
	App\Model;


/**
 * Homepage presenter.
 */
class GroupPresenter extends SecurePresenter{
    
    private $detailId;
    public $id_groups=1;
    public $keyword='';
    
    public function actionDetail($id)
    {
        $this->detailId = $id;
    }
    
    public function createComponentFriendList()
    {
        $list = new \FriendList($this->context->employees, $this->keyword);
        return $list;       
    }
    
    public function createComponentSearcgEmp() {
        $form = $this->createComponentSearch();
        $form->onSuccess = null;
        $form->onSuccess[] =  callback($this, 'processSearchEmp');
        return $form;
    }
    
    public function processSearchEmp($form) {
        $values = $form->getValues();
        $this->keyword = $values['search'];
    }
}