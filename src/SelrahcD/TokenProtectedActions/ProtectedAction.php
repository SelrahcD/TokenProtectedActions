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
		$this->actionId   = $actionId;
		$this->repository = $repository;
	}

	/**
	 * Get a token for user
	 * 
	 * @param  TokenProtectedUserInterface $user
	 * @return string
	 */
	public function getToken(TokenProtectedUserInterface $user)
	{
		return $this->repository->create($user, $this->actionId);
	}

	/**
	 * Execute the callback if a valid token is provided
	 * 
	 * @param  TokenProtectedUserInterface $user     
	 * @param  string                      $token    
	 * @param  Closure                     $callback 
	 * @return mixed                       
	 */
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