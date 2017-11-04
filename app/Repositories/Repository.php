<?php

namespace App\Repositories;

use Doctrine\ORM\EntityManager;
use TempestTools\Scribe\Doctrine\RepositoryAbstract;

class Repository extends RepositoryAbstract
{
	protected $entity;
	
	public function __construct()
	{
		if($this->entity === NULL)
		{
			throw new \RuntimeException('All repositories must specify which entity they are providing a repository for.');
		}
		
		/** @var EntityManager $em */
		$em = app('em');
		
		parent::__construct($em, $em->getClassMetadata($this->entity));
		
		$this->excludeDeleted();
	}
	
	public function excludeDeleted()
	{
		$this->getEntityManager()->getFilters()->enable('deletable');
		
		return $this;
	}
	
	public function includeDeleted()
	{
		$this->getEntityManager()->getFilters()->disable('deletable');
		
		return $this;
	}

	public function getTTConfig(): array
    {
        throw new \RuntimeException('Error: Must be implemented in extended repo');
    }
}