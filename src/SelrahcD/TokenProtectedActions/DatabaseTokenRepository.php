<?php namespace SelrahcD\TokenProtectedActions;

use Illuminate\Database\Connection;
use DateTime;


class DatabaseTokenRepository implements TokenRepositoryInterface {
	
	/**
	 * The database connection instance.
	 *
	 * @var Illuminate\Database\Connection
	 */
	protected $connection;

	/**
	 * The token database table.
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * The hashing key.
	 *
	 * @var string
	 */
	protected $hashKey;

	/**
	 * Valid period of token in ms
	 * 
	 * @var int
	 */
	protected $maxValidTime;

	/**
	 * Create a new token repository instance.
	 *
	 * @var Illuminate\Database\Connection  $connection
	 * @return void
	 */
	public function __construct(Connection $connection, $table, $hashKey, $maxValidTime = 259200)
	{
		$this->table = $table;
		$this->hashKey = $hashKey;
		$this->connection = $connection;
		$this->maxValidTime = $maxValidTime;
	}

	/**
	 * Create a new token for user and action
	 * 
	 * @param  SelrahcD\TokenProtectedActions\TokenProtectedUserInterface $user   
	 * @param  int $action 
	 * @return string
	 */
	public function create(TokenProtectedUserInterface $user, $action)
	{
		$token = $this->createNewToken();
		$this->getTable()->insert($this->getPayload($user->getId(), $action, $token));

		return $token;
	}

	/**
	 * Determine if a token exists for user and action and is valid
	 * 
	 * @param  SelrahcD\TokenProtectedActions\TokenProtectedUserInterface $user   
	 * @param  int $action 
	 * @param  string $token  
	 * @return bool
	 */
	public function exists(TokenProtectedUserInterface $user, $action, $token)
	{
		$permission = $this->getTable()->where('user_id', $user->getId())->where('action', $action)->where('token', $token)->first();

		return $permission && $this->isPermissionValid($permission);
	}

	/**
	 * Delete token
	 * 
	 * @param  string $token
	 * @return void
	 */
	public function delete($token)
	{
		$this->getTable()->where('token', $token)->delete();
	}

	/**
	 * Determine if the permission is valid
	 * 
	 * @param  StdClass  $permission
	 * @return boolean
	 */
	protected function isPermissionValid($permission)
	{
		return (strtotime($permission->created_at) + $this->maxValidTime) > time();
	}

	/**
	 * Begin a new database query against the table.
	 *
	 * @return Illuminate\Database\Query\Builder
	 */
	protected function getTable()
	{
		return $this->connection->table($this->table);
	}

	/**
	 * Get the database connection instance.
	 *
	 * @return Illuminate\Database\Connection
	 */
	public function getConnection()
	{
		return $this->connection;
	}

	/**
	 * Build the record payload for the table.
	 *
	 * @param  int  $user_id
	 * @param  string  $token
	 * @return array
	 */
	protected function getPayload($user_id, $action, $token)
	{
		return array('user_id' => $user_id, 'action' => $action, 'token' => $token, 'created_at' => new DateTime);
	}

	/**
	 * Create a new token.
	 *
	 * @return string
	 */
	public function createNewToken()
	{
		$value = str_shuffle(sha1(spl_object_hash($this).microtime(true)));

		return hash_hmac('sha512', $value, $this->hashKey);
	}


}