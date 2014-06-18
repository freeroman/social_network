<?php

namespace App\Presenters;

use Nette,
	App\Model;


/**
 * Homepage presenter.
 */
class HomepagePresenter extends SecurePresenter
{
    private $emp=null;
    
    /** @persistent */
    public $keyword=null;
    
    public function renderDetail($id)
    {
        $this->template->msgs = $this->context->messages->getMessagesByUserId($id);
        $emp = $this->context->employees->getEmployeeById($id);
        $this->emp = $emp;
        $this->template->emp = $emp;
        $this->template->friends = $this->context->employees->getFriendsById($id);
        $this->template->groups = $this->context->employees->getGroupsById($id, TRUE);
    }
    public function renderDefault()
    {
        $this->template->messages = $this->context->messages->getFriendsMessages($this->user->getId());
        $this->template->events = $this->context->events->getFeedEvents($this->user->getId());
    }
    
    public function createComponentNewMessage()
    {
        $list = new \NewMessage($this->context->messages, $this->user->getId(), $this->emp['id_walls']);
        return $list;
    }
        
    public function renderSearch($keyword) {
        $this->keyword=$keyword;
        //$this->template->result = $this->context->employees->getEmployeesByKeyword($keyword);
        $this->template->friends = $this->context->employees->getFriendsByKeyword($keyword, $this->user->getId());
        $this->template->groups = $this->context->employees->getGroupsByKeyword($keyword);
        $this->template->events = $this->context->events->getEventsByKeyword($keyword);
    }

    public function actionAddFriend($id) {
        $relationship = array(
            'id_employees1' => $this->user->getId(),
            'id_employees2' => $id,
            'created_dt' => date('Y-m-d H-i-s'),
            'valid_from' => '1000-01-01 00:00:00',
            'valid_to' => '2999-12-31 23:59:59',
        );
        $this->context->employees->addFriend($relationship);
        $this->redirect('Homepage:search', $this->keyword);
    }

    public function actionAcceptFriendship($id) {
        $this->context->employees->updateFriendship(array('accepted'=>1), $id);
        $this->redirect('Homepage:default');
    }

    public function actionRemoveFriend($id) {
        $this->context->employees->updateFriendship(array('valid_to'=>date('Y-m-d H-i-s')), $id);
        $this->redirect('Homepage:search');
    }

}
