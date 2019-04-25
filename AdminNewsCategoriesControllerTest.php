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

class NewsCategoriesTest extends TestCase
{
    protected $controller;

    public function setUp()
    {
        parent::setUp();

        $this->app = new Container;

        $this->app['sentinel']   = m::mock('Cartalyst\Sentinel\Sentinel');
        $this->app['view'] = $this->app['Illuminate\Contracts\View\Factory'] = m::mock('Illuminate\View\Factory');
        $this->app['alerts']     = m::mock('Cartalyst\Alerts\Alerts');
        $this->app['translator'] = m::mock('Illuminate\Translation\Translator');
        $this->app['redirect']   = m::mock('Illuminate\Routing\Redirector');
        // Admin Controller expectations
        $this->app['sentinel']->shouldReceive('getUser');
        $this->app['view']->shouldReceive('share');
        $this->app['datagrid']   = m::mock('Cartalyst\DataGrid\DataGrid');
        $this->app['request']    = m::mock('Illuminate\Http\Request');

        $this->app['response'] = $this->app['Illuminate\Contracts\Routing\ResponseFactory'] = m::mock('Symfony\Component\HttpFoundation\Response');

        // Pages Repository
        $this->newscategories = m::mock('Mvs\News\Repositories\Newscategory\NewscategoryRepositoryInterface');

        // Pages Controller
        $this->controller = new NewscategoriesController($this->newscategories);

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
    public function newscategories_index_route()
    {

        $this->app['view']->shouldReceive('make')->once();

        $this->newscategories->shouldReceive('findAll')->once();

        $this->controller->index();
    }

    /** @test */
    public function newscategories_create_route()
    {
        $this->app['view']->shouldReceive('make')
            ->once();
        $this->newscategories->shouldReceive('createModel')->once();
        $this->newscategories->shouldReceive('findAll')->once();

        $this->controller->create();
    }

    /*** @test **/
    public function newscategories_edit_route()
    {
        $this->app['view']->shouldReceive('make')
            ->once();

        $this->newscategories->shouldReceive('createModel')->once();
        $this->newscategories->shouldReceive('findAll')->once();

        $this->newscategories->shouldReceive('find')
            ->once()
            ->andReturn(['id' => '1']);

        $this->controller->create();
    }

    /** @test */
    public function newscategories_edit_non_existing()
    {
        $this->newscategories->shouldReceive('find')
            ->once();

        $this->newscategories->shouldReceive('gridFiltered')
            ->once();

        $this->app['translator']->shouldReceive('trans')
            ->once();

        $this->app['redirect']->shouldReceive('route')
            ->once()
            ->andReturn($response = m::mock('Illuminate\Response\Response'));

        $this->controller->edit(111);
    }

    /** @test **/
    public function newscategories_datagrid()
    {

        $this->newscategories->shouldReceive('gridFiltered')
            ->once();

        $this->controller->grid();
    }

    /** @test */
    public function newscategories_store()
    {
        $this->app['alerts']->shouldReceive('success');

        $this->app['translator']->shouldReceive('trans');

        $this->app['request']->shouldReceive('all')
            ->once()
            ->andReturn(['slug' => 'foo']);

        $this->app['redirect']->shouldReceive('route')
            ->once();

        $message = m::mock('Illuminate\Support\MessageBag');

        $message->shouldReceive('isEmpty')
            ->once()
            ->andReturn(true);

        $this->newscategories->shouldReceive('store')
            ->once()
            ->andReturn([$message, $model = m::mock('Mvs\News\Models\Newscategory')]);

        $this->controller->store();
    }

    /** @test */
    public function newscategories_update_route()
    {
        $this->app['alerts']->shouldReceive('success');

        $this->app['translator']->shouldReceive('trans');

        $this->app['request']->shouldReceive('all')
            ->once()
            ->andReturn(['slug' => 'foo']);

        $this->app['redirect']->shouldReceive('route')
            ->once();

        $message = m::mock('Illuminate\Support\MessageBag');

        $message->shouldReceive('isEmpty')
            ->once()
            ->andReturn(true);

        $this->newscategories->shouldReceive('store')
            ->once()
            ->andReturn([$message ,$model = m::mock('Mvs\News\Models\Newscategory')]);

        $this->controller->update(1);
    }

    /** @test */
    public function newscategories_update_invalid_route()
    {
        $this->app['alerts']->shouldReceive('error');

        $this->app['translator']->shouldReceive('trans');


        $this->app['redirect']->shouldReceive('route')
            ->once()
            ->andReturn($response = m::mock('Illuminate\Response\Response'));

        $this->app['request']->shouldReceive('all')
            ->once()
            ->andReturn(['slug' => 'foo']);

        $message = m::mock('Illuminate\Support\MessageBag');

        $message->shouldReceive('isEmpty')
            ->once()
            ->andReturn(true);


        $this->newscategories->shouldReceive('store')
            ->once()
            ->andReturn([$message ,$model = m::mock('Mvs\News\Models\Newscategory')]);

        $this->controller->update(111);
    }

    /** @test */
    public function delete_route()
    {
        $this->app['alerts']->shouldReceive('success');

        $this->app['translator']->shouldReceive('trans');

        $this->app['redirect']->shouldReceive('route')
            ->once();

        $this->newscategories->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        $this->controller->delete(1);
    }

    /** @test */
    public function delete_not_existing_route()
    {
        $this->app['alerts']->shouldReceive('error');

        $this->app['translator']->shouldReceive('trans');

        $this->app['redirect']->shouldReceive('route')
            ->once();

        $this->newscategories->shouldReceive('delete')
            ->once();

        $this->controller->delete(1);
    }

    /** @test */
    public function execute_action()
    {
        $this->app['request']->shouldReceive('input')
            ->with('action')
            ->once()
            ->andReturn('delete');

        $this->app['request']->shouldReceive('input')
            ->with('rows', [])
            ->once()
            ->andReturn([1]);

        $this->newscategories->shouldReceive('delete')
            ->with(1)
            ->once();

        $this->app['response']
            ->shouldReceive('make')
            ->with('Success', 200, [])
            ->once();

        $this->controller->executeAction();
    }

    /** @test */
    public function execute_non_existing_action()
    {
        $this->app['request']->shouldReceive('input')
            ->with('action')
            ->once()
            ->andReturn('foobar');

        $this->app['response']
            ->shouldReceive('make')
            ->with('Failed', 500, [])
            ->once();

        $this->controller->executeAction();
    }

}