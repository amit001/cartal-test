<?php

namespace Mvs\News\Tests;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Cartalyst\Sentinel\Roles\EloquentRole;
use Mockery as m;
use Mvs\News\Models\Newscategory;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;

class NewscategoryModelTest extends TestCase {


	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setUp()
	{
		parent::setUp();

		$this->newscategory = new Newscategory();
	}

}