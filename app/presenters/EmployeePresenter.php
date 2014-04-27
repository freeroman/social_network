<?php

namespace App\Presenters;

use Nette,
	App\Model;

/**
 * Description of EmployeePresenter
 *
 * @author romanpechal
 */
class EmployeePresenter extends SecurePresenter {    
    
    protected function createComponentEmployeeForm()
        {
            $form = new Nette\Application\UI\Form();
            $form->addText('first_name', 'First name')
                    ->setRequired('Jmeno je povinné');
            $form->addText('surname', 'Surname')
                    ->setRequired('Přijmení je povinné');
            $form->addText('job_title', 'Job title')
                    ->setRequired('Přezdívka je povinná');
            //$form->addDateTimePicker('datum_narozeni', 'Datum narozeni:', 16, 16);
            
            $form->addGroup()
                    ->setOption('container', 'div class=prazdna_skupina');
            
            $form->addGroup('User information')
                    ->setOption('container', 'fieldset id=adress');
            $form->addText('login', 'Login')
                    ->setRequired('Přezdívka je povinná');
            $form->addText('password', 'Password')
                    ->setRequired('Přezdívka je povinná');            
            $form->addText('role', 'Role')
                    ->setRequired('Přezdívka je povinná');
            $form->setCurrentGroup(NULL);
            /*
            $options = array(
                'noAdress' => 'bez adresy',
                'newAdress' => 's adresou',
                'pickAdress' => 'vybrat z existujicich',
            );
            $form->addRadioList('adressRadioList','', $options)
                    ->setDefaultValue('noAdress')
                    ->getSeparatorPrototype()->setName(NULL);
            //$form->addCheckbox('adressChb', 'Adresa');
                  //  ->setAttribute('id', 'adressChb');
            
            $form->addGroup('Adresa')
            
            $adresses = $this->context->persons->getAdressPairs();            
            $form->addSelect('adresSelect', 'Adresy', $adresses);
            
            $form->addText('mesto', 'Město');
            $form->addText('ulice', 'Ulice');
            $form->addText('cislo_domu', 'Číslo domu')
                    ->addCondition(Nette\Application\UI\Form::FILLED)
                        ->addRule(Nette\Application\UI\Form::INTEGER, 'Číslo domu musí být číslo');                    
            $form->addText('psc', 'PSČ')
                    ->addCondition(Nette\Application\UI\Form::FILLED)
                        ->addRule(Nette\Application\UI\Form::INTEGER, 'PSČ musí být číslo')
                        ->addRule(Nette\Application\UI\Form::MAX_LENGTH, 'PSČ může být dlouhé max 5 číslic', 5);
            $form->setCurrentGroup(NULL);*/
            $form->addSubmit('send', 'Vytvořit');
            //$form->addCheckbox('save_adress', 'Adresa');*/
            
            $renderer = $form->getRenderer();
                $renderer->wrappers['controls']['container'] = NULL;
                $renderer->wrappers['pair']['container'] = 'div class=form-group';
                $renderer->wrappers['pair']['.error'] = 'has-error';
                $renderer->wrappers['control']['container'] = 'div class=col-sm-9';
                $renderer->wrappers['label']['container'] = 'div class="col-sm-3 control-label"';
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
            
            $form->onValidate[] = callback($this, 'validateEmployeeForm');
            
            $form->onSuccess[] =  callback($this, 'processEmployeeForm');
            return $form;
        }
        
        public function processEmployeeForm($form){
            $values = $form->getValues();
            $employee = array(
                'login' => $values['login'],
                'first_name' => $values['first_name'],
                'surname' => $values['surname'],
                'birth_dt' => date('Y-m-d'),
                'job_title' => $values['job_title'],
                'id_walls' => $this->context->employees->newWall(),
                'password' => \App\Model\Passwords::hash($values['password']),
                'role' => $values['role']
            );
            $this->context->authorizator->add($employee);
            $this->flashMessage('You have succesfully added new employee.');
            $this->redirect('employee:');
        }
        
        public function validateEmployeeForm($form){
            $values = $form->getValues();
            if ($this->context->employees->loginExists($values['login'])){
                $form->addError('login existuje');
            }
        }
}
