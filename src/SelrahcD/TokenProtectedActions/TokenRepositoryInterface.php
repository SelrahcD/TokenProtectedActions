<?php namespace SelrahcD\TokenProtectedActions;

interface TokenRepositoryInterface {
	
	/**
	 * Create a new token for user and action
	 * @param  SelrahcD\TokenProtectedActions\TokenProtectedUserInterface $user   
	 * @param  int $action 
	 * @return string
	 */
	public function create(TokenProtectedUserInterface $user, $action);

	/**
	 * Determine if a token exists for user and action and is valid
	 * @param  SelrahcD\TokenProtectedActions\TokenProtectedUserInterface $user   
	 * @param  int $action 
	 * @param  string $token  
	 * @return bool
	 */
	public function exists(TokenProtectedUserInterface $user, $action, $token);

	/**
	 * Delete token
	 * @param  string $token [description]
	 * @return void
	 */
	public function delete($token);
}