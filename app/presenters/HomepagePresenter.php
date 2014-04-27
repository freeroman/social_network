<?php

namespace App\Presenters;

use Nette,
	App\Model;


/**
 * Homepage presenter.
 */
class HomepagePresenter extends SecurePresenter
{

	public function renderDefault()
	{
		$this->template->msgs = $this->context->messages->getMessagesByUserId($this->user->getId());
	}
        
        protected function createComponentNewMessage()
        {
            $form = new Nette\Application\UI\Form();
            
            $form->addTextArea('text', 'Message')
                    ->setRequired('Text of message can\'t be empty.')
                    ->setAttribute('class', 'form-control');
            
            $form->addSubmit('send', 'Vytvořit')
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

            foreach ($form->getControls() as $control) {
                    if ($control instanceof Controls\Button) {
                            //$control->getControlPrototype()->addClass(empty($usedPrimary) ? 'btn btn-default' : 'btn btn-default');
                            //$usedPrimary = TRUE;
                        $control->getControlPrototype()->addClass('btn btn-default');

                    } elseif ($control instanceof Controls\TextBase || $control instanceof Controls\SelectBox || $control instanceof Controls\MultiSelectBox) {
                            $control->getControlPrototype()->addClass('form-control');

                    } elseif ($control instanceof Controls\Checkbox || $control instanceof Controls\CheckboxList || $control instanceof Controls\RadioList) {
                            $control->getSeparatorPrototype()->setName('div')->addClass($control->getControlPrototype()->type);
                    }
            }
            
            //$form->onValidate[] = callback($this, 'validateNewMessage');
            
            $form->onSuccess[] =  callback($this, 'processNewMessage');
            
            return $form;
        }
        
        public function processNewMessage($form){
            $values = $form->getValues();
            $message = array(
                'id_walls' => $this->template->employee->id_walls,
                'id_employees' => $this->template->employee->id_employees,
                'text' => $values['text'],
                'created_dt' => date('Y-m-d H-i-s'),
                'visible_from' => '1000-01-01 00:00:00',
                'visible_to' => '2999-12-31 23:59:59',
                'id_sharing_types' => '1'
            );
            $this->context->messages->insertMessage($message);
            $this->flashMessage('Message has been posted.');
            $this->redirect('Homepage:');
        }

}
