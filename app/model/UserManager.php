<?php

namespace App\Model;

use Nette,
	Nette\Utils\Strings;


/**
 * Users management.
 */
class UserManager extends Nette\Object implements Nette\Security\IAuthenticator
{
	const
		TABLE_NAME = 'employees',
		COLUMN_ID = 'id_employees',
		COLUMN_NAME = 'login',
		COLUMN_PASSWORD_HASH = 'password',
		COLUMN_ROLE = 'role';


	/** @var DibiConnection */
	private $database;


	public function __construct(\DibiConnection $database)
	{
		$this->database = $database;
	}


	/**
	 * Performs an authentication.
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;

		$row = $this->database->select('*')->from(self::TABLE_NAME)->where(self::COLUMN_NAME.'=%s', $username)->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);

		} elseif (!Passwords::verify($password, $row[self::COLUMN_PASSWORD_HASH])) {
			throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);

		} elseif (Passwords::needsRehash($row[self::COLUMN_PASSWORD_HASH])) {
			$row->update(array(
				self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
			));
		}

		$arr = $row->toArray();
		unset($arr[self::COLUMN_PASSWORD_HASH]);
		return new Nette\Security\Identity($row[self::COLUMN_ID], $row[self::COLUMN_ROLE], $arr);
	}


	/**
	 * Adds new user.
	 * @param  string
	 * @param  string
	 * @return void
	 */
	public function add($data)
	{
            $this->database->insert(self::TABLE_NAME, $data)->execute();
	}

}
