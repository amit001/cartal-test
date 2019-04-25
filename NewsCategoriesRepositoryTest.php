<?php

namespace Mvs\News\Tests;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Cartalyst\Sentinel\Roles\EloquentRole;
use Mockery as m;
use Mvs\News\Controllers\Admin\NewscategoriesController;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;

class NewsCategoriesRepositoryTest extends TestCase
{
    protected $controller;

    public function setUp()
    {
        parent::setUp();

        $this->app = new Container;

        $this->app['events']     = m::mock('Illuminate\Events\Dispatcher');

        $this->app['handler.data']     = m::mock('Mvs\News\Handlers\Newscategory\NewscategoryDataHandlerInterface');

        $this->repository = m::mock('Mvs\News\Repositories\Newscategory\NewscategoryRepository[createModel]', [$this->app]);

    }


    /** @test **/
	public function it_can_generate_the_grid()
	{
		$this->repository->shouldReceive('createModel')
			->once()
			->andReturn($model = m::mock('Illuminate\Database\Eloquent\Model'));

		$this->repository->grid();
	}


}