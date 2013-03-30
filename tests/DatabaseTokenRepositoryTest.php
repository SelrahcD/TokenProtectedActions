<?php

use Mockery as m;

class DatabaseTokenRepositoryTest extends PHPUnit_Framework_TestCase {
	
	public function tearDown()
	{
		m::close();
	}

	public function testCreateInsertsNewRecordIntoTable()
	{
		$repo = $this->getRepo();
		$repo->getConnection()->shouldReceive('table')->once()->with('table')->andReturn($query = m::mock('StdClass'));
		$query->shouldReceive('insert')->once();
		$user = m::mock('SelrahcD\TokenProtectedActions\TokenProtectedUserInterface');
		$user->shouldReceive('getId')->andReturn(1);

		$results = $repo->create($user, 1);

		$this->assertTrue(is_string($results) and strlen($results) > 1);
	}

	public function testExistReturnsFalseIfNoRowFoundForUserOrAction()
	{
		$repo = $this->getRepo();
		$repo->getConnection()->shouldReceive('table')->once()->with('table')->andReturn($query = m::mock('StdClass'));
		$query->shouldReceive('where')->once()->with('user_id', 1)->andReturn($query);
		$query->shouldReceive('where')->once()->with('action', 1)->andReturn($query);
		$query->shouldReceive('where')->once()->with('token', 'token')->andReturn($query);
		$query->shouldReceive('first')->andReturn(null);
		$user = m::mock('SelrahcD\TokenProtectedActions\TokenProtectedUserInterface');
		$user->shouldReceive('getId')->andReturn(1);

		$this->assertFalse($repo->exists($user, 1, 'token'));
	}

	public function testExistReturnsTrueIfValidRecordExists()
	{
		$repo = $this->getRepo();
		$repo->getConnection()->shouldReceive('table')->once()->with('table')->andReturn($query = m::mock('StdClass'));
		$query->shouldReceive('where')->once()->with('user_id', 1)->andReturn($query);
		$query->shouldReceive('where')->once()->with('action', 1)->andReturn($query);
		$query->shouldReceive('where')->once()->with('token', 'token')->andReturn($query);
		$date = date('Y-m-d H:i:s', time() - 5);
		$query->shouldReceive('first')->andReturn((object) array('created_at' => $date));
		$user = m::mock('SelrahcD\TokenProtectedActions\TokenProtectedUserInterface');
		$user->shouldReceive('getId')->andReturn(1);

		$this->assertTrue($repo->exists($user, 1, 'token'));
	}

	public function testDeleteMethodDeletesByToken()
	{
		$repo = $this->getRepo();
		$repo->getConnection()->shouldReceive('table')->once()->with('table')->andReturn($query = m::mock('StdClass'));
		$query->shouldReceive('where')->once()->with('token', 'token')->andReturn($query);
		$query->shouldReceive('delete')->once();

		$repo->delete('token');
	}

	public function testExistReturnsFalseIfRecordIsExpired()
	{
		$repo = $this->getRepo();
		$repo->getConnection()->shouldReceive('table')->once()->with('table')->andReturn($query = m::mock('StdClass'));
		$query->shouldReceive('where')->once()->with('user_id', 1)->andReturn($query);
		$query->shouldReceive('where')->once()->with('action', 1)->andReturn($query);
		$query->shouldReceive('where')->once()->with('token', 'token')->andReturn($query);
		$date = date('Y-m-d H:i:s', time() - 3000);
		$query->shouldReceive('first')->andReturn((object) array('created_at' => $date));
		$user = m::mock('SelrahcD\TokenProtectedActions\TokenProtectedUserInterface');
		$user->shouldReceive('getId')->andReturn(1);

		$this->assertFalse($repo->exists($user, 1, 'token'));
	}


	protected function getRepo()
	{
		return new SelrahcD\TokenProtectedActions\DatabaseTokenRepository(m::mock('Illuminate\Database\Connection'), 'table', 'key', 10);
	}
}