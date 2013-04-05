<?php

use Mockery as m;

class ProtectedActionTest extends PHPUnit_Framework_TestCase {
	
	public function tearDown()
	{
		m::close();
	}

	public function testATokenCanBeCreatedForUser()
	{
		$user = m::mock('SelrahcD\TokenProtectedActions\TokenProtectedUserInterface');
		$repo = m::mock('SelrahcD\TokenProtectedActions\TokenRepositoryInterface');

		$repo->shouldReceive('create')->once()->with($user, 1)->andReturn('token');

		$action = new SelrahcD\TokenProtectedActions\ProtectedAction(1, $repo);
		$token = $action->getToken($user);

		$this->assertTrue(is_string($token) and $token == 'token');
	}

	public function testExecuteReturnsFalseIfUserHasNoValidToken()
	{
		$user = m::mock('SelrahcD\TokenProtectedActions\TokenProtectedUserInterface');
		$repo = m::mock('SelrahcD\TokenProtectedActions\TokenRepositoryInterface');
		$repo->shouldReceive('exists')->once()->with($user, 'token', 1)->andReturn(false);

		$action = new SelrahcD\TokenProtectedActions\ProtectedAction(1, $repo);
		$result = $action->execute($user, 'token', function(){});

		$this->assertFalse($result);
	}

	public function testActionCallbackIsCalledIfUserHasAValidTokenAndReturnCallbackValue()
	{
		$user = m::mock('SelrahcD\TokenProtectedActions\TokenProtectedUserInterface');
		$repo = m::mock('SelrahcD\TokenProtectedActions\TokenRepositoryInterface');
		$repo->shouldReceive('exists')->once()->with($user, 'token', 1)->andReturn(true);
		$repo->shouldReceive('delete')->once();
		$action = new SelrahcD\TokenProtectedActions\ProtectedAction(1, $repo);

		$callback = function()
		{
			$_SERVER['__protectedCallback'] = 'callback called';
			return 'value';
		};

		$callbackValue = $action->execute($user, 'token', $callback);

		$this->assertEquals('callback called', $_SERVER['__protectedCallback']);
		$this->assertEquals('value', $callbackValue);
	}

	public function testTokenIsSupressedIfCallbackReturnsTrue()
	{
		$user = m::mock('SelrahcD\TokenProtectedActions\TokenProtectedUserInterface');
		$repo = m::mock('SelrahcD\TokenProtectedActions\TokenRepositoryInterface');
		$repo->shouldReceive('exists')->once()->with($user, 'token', 1)->andReturn(true);
		$repo->shouldReceive('delete')->once()->with('token');
		$action = new SelrahcD\TokenProtectedActions\ProtectedAction(1, $repo);

		$callbackValue = $action->execute($user, 'token', function()
			{
				return 'value';
			});
	}

	public function testTokenIsNotSupressedIfCallbackReturnsFalse()
	{
		$user = m::mock('SelrahcD\TokenProtectedActions\TokenProtectedUserInterface');
		$repo = m::mock('SelrahcD\TokenProtectedActions\TokenRepositoryInterface');
		$repo->shouldReceive('exists')->once()->with($user, 'token', 1)->andReturn(true);
		$action = new SelrahcD\TokenProtectedActions\ProtectedAction(1, $repo);

		$callbackValue = $action->execute($user, 'token', function()
			{
				return false;
			});
	}
}