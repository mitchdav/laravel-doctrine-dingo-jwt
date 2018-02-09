<?php

use App\API\V1\Entities\User;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/** @var EntityManagerInterface $em */
	protected $em;
	
	/**
	 * DatabaseSeeder constructor.
	 *
	 * @param EntityManagerInterface $em
	 */
	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}
	
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$generator = Factory::create();
		
		$user = new User();
		$user
			->setEmail($generator->safeEmail)
			->setPassword('password')
			->setName($generator->name)
			->setJob($generator->jobTitle)
            ->setAddress($generator->address);
		
		$this->em->persist($user);
		
		$this->command->comment('Created user with email "' . $user->getEmail() . '" and password "' . 'password' . '".');
		
		$this->em->flush();
	}
}