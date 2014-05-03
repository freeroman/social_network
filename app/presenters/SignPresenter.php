<?php

namespace App\Presenters;

use Nette,
	App\Model;


/**
 * Sign in/out presenters.
 */
class SignPresenter extends BasePresenter
{


	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm()
	{
		$form = new Nette\Application\UI\Form;
		$form->addText('username', 'Username:')
			->setRequired('Please enter your username.');

		$form->addPassword('password', 'Password:')
			->setRequired('Please enter your password.');

		$form->addCheckbox('remember', 'Keep me signed in');

		$form->addSubmit('send', 'Sign in');
                
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

		// call method signInFormSucceeded() on success
		$form->onSuccess[] = $this->signInFormSucceeded;
		return $form;
	}


	public function signInFormSucceeded($form)
	{
		$values = $form->getValues();
                //$this->getUser()->getAuthenticator()->add('roman', 'test');

		if ($values->remember) {
			$this->getUser()->setExpiration('14 days', FALSE);
		} else {
			$this->getUser()->setExpiration('20 minutes', TRUE);
		}

		try {
			$this->getUser()->login($values->username, $values->password);
			$this->redirect('Homepage:default');

		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}


	public function actionOut()
	{
		$this->getUser()->logout(TRUE);
		$this->flashMessage('You have been signed out.');
		$this->redirect('in');
	}

}
