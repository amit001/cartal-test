<?php

namespace Mvs\News\Tests;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Cartalyst\Sentinel\Roles\EloquentRole;
use Mockery as m;
use Mvs\News\Controllers\Admin\NewsController;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;

class AdminNewsControllerTest extends TestCase
{
    protected $controller;

    public function setUp()
    {
        parent::setUp();

        $this->app = new Container;

        $this->app['sentinel']   = m::mock('Cartalyst\Sentinel\Sentinel');
        $this->app['view'] = $this->app['Illuminate\Contracts\View\Factory'] = m::mock('Illuminate\View\Factory');
        $this->app['alerts']     = m::mock('Platform\Foundation\Controllers\Controller');
        $this->app['translator'] = m::mock('Illuminate\Translation\Translator');
        $this->app['redirect']   = m::mock('Illuminate\Routing\Redirector');
        // Admin Controller expectations
        $this->app['sentinel']->shouldReceive('getUser');
        $this->app['view']->shouldReceive('share');
        $this->app['datagrid']   = m::mock('Cartalyst\DataGrid\DataGrid');
        $this->app['request']    = m::mock('Illuminate\Http\Request');

        $this->app['response'] = $this->app['Illuminate\Contracts\Routing\ResponseFactory'] = m::mock('Symfony\Component\HttpFoundation\Response');

        // Pages Repository
        $this->news = m::mock('Mvs\News\Repositories\News\NewsRepositoryInterface');
        $this->newscategories = m::mock('Mvs\News\Repositories\Newscategory\NewscategoryRepositoryInterface');

        // Pages Controller
        $this->controller = new NewsController($this->news, $this->newscategories);

        // Set the container instance
        Container::setInstance($this->app);

        // Set the facade container
        Facade::setFacadeApplication($this->app);$this->app['redirect']   = m::mock('Illuminate\Routing\Redirector');
    }

    protected function trans($times = 1)
    {
        $this->app['translator']->shouldReceive('trans')
            ->times($times);

        return $this;
    }

    /**
     * Set a redirect method expectation.
     *
     * @param  $method  string
     * @return this
     */
    protected function redirect($method)
    {
        $this->app['redirect']->shouldReceive($method)
            ->once()
            ->andReturn($this->app['redirect']);

        return $this;
    }

    /** @test */
    public function news_index_route()
    {
        $this->app['view']->shouldReceive('make')->once();

        $this->controller->index();
    }

    /** @test */
    public function newscategories_create_route()
    {
        $this->app['view']->shouldReceive('make')->once();
        $this->news->shouldReceive('createModel')->once();
        $this->news->shouldReceive('findAll')->once();
        $this->newscategories->shouldReceive('findAll')->once();
        $this->news->shouldReceive('relatedNews')->once();

        $this->controller->create();
    }

    /** @test **/
    public function newscategories_edit_route()
    {
        $this->app['view']->shouldReceive('make')->once();
        $this->news->shouldReceive('find')->once()->andReturn(['salkjd'=>'sldkjl']);
        $this->news->shouldReceive('findAll')->once();
        $this->newscategories->shouldReceive('findAll')->once();
        $this->news->shouldReceive('relatedNews')->once();

        $this->controller->edit('update', 1);
    }

    /** @test */
    public function newscategories_edit_non_existing()
    {
        $this->news->shouldReceive('find')->once()->andReturn(null);

        $this->app['translator']->shouldReceive('trans')
            ->once();

        $this->app['redirect']->shouldReceive('route')
            ->once()
            ->andReturn($response = m::mock('Illuminate\Response\Response'));

        $this->news->shouldReceive('find');

        $this->controller->edit(1);
    }

}