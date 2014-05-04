<?php

namespace App\Presenters;

use Nette,
	App\Model;

class GroupPresenter extends SecurePresenter{
    
    //private $detailId;
    public $id_groups=1;
    
    public function actionDefault($id)
    {
        $this->id_groups = $id;
    }
    
    public function createComponentFriendList()
    {
        $list = new \FriendList($this->context->employees);
        return $list;       
    }
}