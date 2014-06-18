<?php

namespace App\Presenters;

use Nette,
	App\Model;

/**
 * Description of EmployeePresenter
 *
 * @author romanpechal
 */
class MessagePresenter extends SecurePresenter {    
    private $data;
    
    protected function createComponentNewMessage()
    {
        $form = new Nette\Application\UI\Form();
        
        $form->addTextArea('text', 'Message')
                ->setRequired('Text of message can\'t be empty.')
                ->setAttribute('class', 'form-control');
        
        $form->addUpload('file', 'File or image')
                ->addRule(\Nette\Application\UI\Form::MAX_FILE_SIZE, 'File is too large (maximum 512 kB).', 512 * 1024 /* v bytech */);

        $form->addSubmit('send', 'Post')
                ->setAttribute('class', 'btn btn-default');

        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = NULL;
        $renderer->wrappers['pair']['container'] = 'div class=form-group';
        $renderer->wrappers['pair']['.error'] = 'has-error';
        //$renderer->wrappers['control']['container'] = 'div class=form-control';
        $renderer->wrappers['label']['container'] = 'small';
        $renderer->wrappers['control']['description'] = 'span class=help-block';
        $renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';

        // make form and controls compatible with Twitter Bootstrap
        $form->getElementPrototype()->class('form-horizontal');
        //$form->onValidate[] = callback($this, 'validateNewMessage');

        $form->onSuccess[] =  callback($this, 'processNewMessage');            
        return $form;
    }
    
    public function actionEdit($id) {
        $form = $this['newMessage'];

        $message = $this->context->messages->getMessageById($id);

        $form->setDefaults($message);

        if (!$message) { // kontrola existence záznamu
            throw new BadRequestException;
        }
        $form->addHidden('id_messages', $id);
        $form->addHidden('created_dt', $message->created_dt);
        $form->addHidden('id_walls', $message->id_walls);
        $form->addHidden('id_employees', $message->id_employees);

        $form['send']->caption = 'Save changes';
        $this->template->form = $form;
    }
    
    public function actionDelete($id) {
        $message = $this->context->messages->getMessageById($id);
        if($message->id_employees===$this->getUser()->getId()){
            $this->context->messages->deleteMessage($id);
            $this->flashMessage('Message has been deleted', 'success');
            $this->redirect('Homepage:');
        } else {
            $this->flashMessage('You do not have permission to do this.', 'warning');
            $this->redirect('Homepage:');
        }
    }
    
    public function processNewMessage($form){
        $values = $form->getValues();
        
        $message = array(
            'text' => $values['text'],
            'created_dt' => $values['created_dt'],
            'visible_from' => date('Y-m-d H-i-s'),
            'id_walls' => $values['id_walls'],
            'id_employees' => $values['id_employees'],
            'visible_to' => '2999-12-31 23:59:59',
            'id_sharing_types' => '1'
        );
        
        $this->context->messages->deleteMessage($values['id_messages']);
        $messageId = $this->context->messages->insertMessage($message);
        
        $file = $form['file']->getValue();
        
        if($file->name){
            $fileName = uniqid() . $file->name;
            if($file->isOk()){
                $imgUrl = __DIR__ . '/../../www/images/files/'.$fileName;
                $file->move($imgUrl);
                
                $file = array(                    
                    'file' => $file->name ? $fileName : null,
                    'id_messages' => $messageId,
                    'id_employees' => $values['id_employees'],
                    'created_dt' => date('Y-m-d H-i-s'),
                    'visible_from' => '1000-01-01 00:00:00',
                    'visible_to' => '2999-12-31 23:59:59',
                    'type' => 'I',
                    'id_sharing_types' => '1'                    
                );
                $this->context->messages->insertFile($file);
            } else {
                $form->addError('Try reupload the picture.');
            }
        } else {            
            $this->context->messages->reAssignFile($values['id_messages'], $messageId);            
        }
        
        $this->flashMessage('Message has been edited.', 'success');
        $this->redirect('this');
    }
}
