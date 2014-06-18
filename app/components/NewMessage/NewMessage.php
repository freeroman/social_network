<?php

/**
 * Description of NewMessage
 *
 * @author pecha_000
 */
class NewMessage extends \Nette\Application\UI\Control
{
    private $messageService;
    
    private $wall;
    
    private $emp;
            
    public function __construct(MessageService $service, $emp, $wall)
    {
        parent::__construct();
        $this->messageService = $service;
        $this->wall = $wall;
        $this->emp = $emp;
    }
    
    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/message.latte');
        $template->render();
    }
    
    protected function createComponentNewMessage()
    {
        $form = new Nette\Application\UI\Form();

        $form->addHidden('id_walls', $this->wall);
        $form->addHidden('id_employees', $this->emp);
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
        
    public function processNewMessage($form){
        $values = $form->getValues();
        
        $message = array(
            'id_walls' => $values['id_walls'],
            'id_employees' => $values['id_employees'],
            'text' => $values['text'],
            'created_dt' => date('Y-m-d H-i-s'),
            'visible_from' => '1000-01-01 00:00:00',
            'visible_to' => '2999-12-31 23:59:59',
            'id_sharing_types' => '1'
        );
        $messageId = $this->messageService->insertMessage($message);
        
        $file = $form['file']->getValue();
        
        if($file->name){
            $fileName = uniqid() . $file->name;
            if($file->isOk()){
                $imgUrl = __DIR__ . '/../../../www/images/files/'.$fileName;
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
                $this->messageService->insertFile($file);
            } else {
                $form->addError('Try reupload the picture.');
            }
        }
        
        $this->flashMessage('Message has been posted.', 'success');
        $this->redirect('this');
    }
}
