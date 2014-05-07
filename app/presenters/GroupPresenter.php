<?php

namespace App\Presenters;

use Nette,
	App\Model;

class GroupPresenter extends SecurePresenter{
    
    //private $detailId;
    public $id_groups=1;
    
    private $wall;
    
    public function actionAdd($id)
    {
        $this->id_groups = $id;
    }
    
    public function renderDefault() {
        $this->template->groups = $this->context->employees->getGroupsWithNewestMessage($this->user->getId());
    }
    
    public function renderDetail($id) {
        $group = $this->context->employees->getGroupById($id);
        $this->template->group = $group;
        $this->wall = $group->id_walls;
        $this->template->messages = $this->context->messages->getMessagesByGroupId($id);
    }
    
    public function createComponentFriendList()
    {
        $list = new \FriendList($this->context->employees);
        return $list;       
    }
    
    public function createComponentNewMessage()
    {
        $list = new \NewMessage($this->context->messages, $this->user->getId(), $this->wall);
        return $list;
    }
}