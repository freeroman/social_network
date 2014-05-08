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
        $form->addGroup('User information');
        $form->addText('first_name', 'First name*')
                ->setRequired('Name is mandatory')
                ->setAttribute('class', 'form-control');
        $form->addText('surname', 'Surname*')
                ->setRequired('Surname is mandatory')
                ->setAttribute('class', 'form-control');
        $form->addText('job_title', 'Job title*')
                ->setRequired('Job title is mandatory')
                ->setAttribute('class', 'form-control');
        //$form->addDateTimePicker('datum_narozeni', 'Datum narozeni:', 16, 16);
        $form->setCurrentGroup(NULL);

        //$form->addGroup()
        //        ->setOption('container', 'div class=prazdna_skupina');

        $form->addGroup('Account info')
                ->setOption('container', 'fieldset id=adress');
        $form->addText('login', 'Login*')
                ->setRequired('Login is mandatory')
                ->setAttribute('class', 'form-control');
        $form->addText('password', 'Password*')
                ->setRequired('Password is mandatory')
                ->setAttribute('class', 'form-control');            
        $form->addText('role', 'Role*')
                ->setRequired('Role is mandatory')
                ->setAttribute('class', 'form-control');
        $form->addUpload('avatar', 'Portrait')
                ->addRule(\Nette\Application\UI\Form::IMAGE, 'File has to be image');
                //->addRule(\Nette\Application\UI\Form::MAX_FILE_SIZE, 'File is too large (maximum 64 kB).', 64 * 1024 /* v bytech */);
        $form->addSubmit('send', 'Create')
            ->setAttribute('class', 'btn btn-default');

        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = 'div class=col-md-4';
        //$renderer->wrappers['pair']['container'] = 'div class=form-group';
        $renderer->wrappers['pair']['.error'] = 'has-error';
        $renderer->wrappers['control']['container'] = 'div class=form-group';
        //$renderer->wrappers['label']['container'] = 'small';
        $renderer->wrappers['control']['description'] = 'span class=help-block';
        $renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';

        $form->onValidate[] = callback($this, 'validateEmployeeForm');

        $form->onSuccess[] =  callback($this, 'processEmployeeForm');
        return $form;
    }

    public function processEmployeeForm($form){        
        $values = $form->getValues();
        
        $file = $form['avatar']->getValue();
        
        $fileName = 'avatar_' . uniqid() . $file->name;
        
        if($file->isOk()){
            $imgUrl = __DIR__ . '/../../www/images/portraits/'.$fileName;
            $file->move($imgUrl);
        } else {
            $form->addError('Try reupload the picture.');
        }

        /*
        if ($id > 0) {
            $this->context->createAkce()->find($id)->update(array(
                'datumOd' => $form->values->datumOd,
                'datumDo' => $form->values->datumDo,
                'popis' => $form->values->text,
                'url' => '/images/upload/' . $file->name
            ));

            $this->flashMessage('Akce upravena.', 'success');
        } else {
            $this->context->createAkce()->insert(array(
                'datumOd' => $form->values->datumOd,
                'datumDo' => $form->values->datumDo,
                'popis' => $form->values->text,
                'url' => '/images/upload/' . $file->name
            ));

            $this->flashMessage('Upload zrušen.', 'success');
        }*/
        
        $employee = array(
            'login' => $values['login'],
            'first_name' => $values['first_name'],
            'surname' => $values['surname'],
            'birth_dt' => date('Y-m-d'),
            'job_title' => $values['job_title'],
            'id_walls' => $this->context->employees->newWall(),
            'password' => \App\Model\Passwords::hash($values['password']),
            'role' => $values['role'],
            'avatar' => $fileName
        );
        $this->context->authorizator->add($employee);
        $this->flashMessage('You have succesfully added new employee.');
        $this->redirect('employee:');
    }

    public function validateEmployeeForm($form){
        $values = $form->getValues();
        if ($this->context->employees->loginExists($values['login'])){
            $form->addError('Login already exists.');
        }
    }
}
