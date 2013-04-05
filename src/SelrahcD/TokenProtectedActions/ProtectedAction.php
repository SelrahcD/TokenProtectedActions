<?php namespace SelrahcD\TokenProtectedActions;

use \Closure;

class ProtectedAction {

	/**
	 * ActionId
	 * 
	 * @var int
	 */
	private $actionId;

	/**
	 * The token repository
	 * 
	 * @var SelrahcD\TokenProtectedActions\TokenRepositoryInterface
	 */
	private $repository;

	/**
	 * Constructor
	 * 
	 * @param TokenRepositoryInterface $repository
	 */
	public function __construct($actionId, TokenRepositoryInterface $repository)
	{
		$this->actionId = $actionId;
		$this->repository = $repository;
	}

	public function getToken(TokenProtectedUserInterface $user)
	{
		return $this->repository->create($user, $this->actionId);
	}

	public function execute(TokenProtectedUserInterface $user, $token, Closure $callback)
	{
		if(!$this->repository->exists($user, $token, $this->actionId))
		{
			return false;
		}

		$response = call_user_func($callback);

		if($response)
		{
			$this->repository->delete($token);
		}

		return $response;
	}
	
}