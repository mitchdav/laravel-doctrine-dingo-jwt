<?php

namespace App\Entities\Traits;

use Doctrine\ORM\Mapping AS ORM;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

trait Authenticatable
{
	use AuthenticatableTrait;
	
	/**
	 * @ORM\Column(name="email", type="string", unique=true)
	 * @var string $email
	 */
	protected $email;
	
	/**
	 * @ORM\Column(name="password", type="string")
	 * @var string $password
	 */
	protected $password;
	
	/**
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}
	
	/**
	 * @param string $email
	 *
	 * @return $this
	 */
	public function setEmail($email)
	{
		$this->email = $email;
		
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}
	
	/**
	 * @param string $password
	 *
	 * @return $this
	 */
	public function setPassword($password)
	{
		$this->password = password_hash($password, PASSWORD_BCRYPT);
		
		return $this;
	}
	
	/**
	 * @param string $password
	 *
	 * @return boolean
	 */
	public function matchesPassword($password)
	{
		return password_verify($password, $this->getPassword());
	}
}