<?php namespace Tuxion\DoctrineRest\_Config;

use \Exception;
use Aura\Web\Request;
use Aura\Router\Router;
use Aura\Di\_Config\AbstractContainerTest;

class CommonTest extends AbstractContainerTest
{
  
  protected $router;
  protected $dummyEntity = 'Tuxion\DoctrineRest\Domain\Dummy\DummyEntity';
  protected $executionTracker;
  
  protected function getConfigClasses()
  {
    return array(
      'Aura\Web\_Config\Common',
      'Tuxion\DoctrineRest\_Config\Test', //This is for including our required stubs.
      'Tuxion\DoctrineRest\_Config\Common',
    );
  }
  
  public function provideNewInstance()
  {
    return array(
      array('Tuxion\DoctrineRest\Action\ActionFactory'),
      array('Tuxion\DoctrineRest\Domain\Composite\CompositeCall'),
      array('Tuxion\DoctrineRest\Domain\Composite\CompositeCallFactory'),
      array('Tuxion\DoctrineRest\Domain\Driver\DoctrineDriver'),
      array('Tuxion\DoctrineRest\Domain\Result\ResultFactory'),
      array('Tuxion\DoctrineRest\Responder\RestResponder'),
      array('Tuxion\DoctrineRest\Responder\StatusCodes'),
      array('Tuxion\DoctrineRest\Mapper\ResourceMapper', array(
        'routePrefix' => '/rest'
      )),
      array('Tuxion\DoctrineRest\Mapper\ResourceFactory'),
      array('Tuxion\DoctrineRest\Mapper\Resource', array(
        'actions' => '*',
        'model' => $this->dummyEntity,
        'resource' => 'instantiation-dummy'
      ))
    );
  }
  
  public function setUp()
  {
    parent::setUp();
    $this->executionTracker = (object)array(
      'before' => false,
      'after' => false
    );
    $this->router = $this->generateRoutes();
  }
  
  public function testGetDummyRequest()
  {
    
    //Reset the tracker here.
    $this->resetTracker();
    
    //Insert example dummy.
    $driver = $this->di->newInstance('Tuxion\DoctrineRest\Domain\Driver\DoctrineDriver');
    $body = array('title' => 'Testing 1, 2, 3...');
    $output = $driver->create($this->dummyEntity, $body);
    
    //Go through a request.
    $router = $this->router;
    $response = $this->executeFakeRequest($router, 'GET', '/rest/instantiation-dummy/1');
    
    //Assert the response.
    $expect = array(
      'status' => 'HTTP/1.1 200 OK',
      'body' => json_encode(array(
        'id' => 1,
        'title' => 'Testing 1, 2, 3...'
      ))
    );
    
    $this->assertEquals($expect, array(
      'status' => $response->status->get(),
      'body' => $response->content->get()
    ));
    
    $this->assertEquals((object)array('before' => true, 'after' => true), $this->executionTracker);
    
  }
  
  public function testPostDummyRequest()
  {
    
    //Reset the tracker here.
    $this->resetTracker();
    
    //Go through a request.
    $router = $this->router;
    $body = json_encode(array(
      'title' => 'Testing 4, 5, 6...'
    ));
    $response = $this->executeFakeRequest($router, 'POST', '/rest/instantiation-dummy', $body);
    
    //Assert the response.
    $expect = array(
      'status' => 'HTTP/1.1 201 Created',
      'body' => json_encode(array(
        'id' => 1,
        'title' => 'Testing 4, 5, 6...'
      ))
    );
    
    $this->assertEquals($expect, array(
      'status' => $response->status->get(),
      'body' => $response->content->get()
    ));
    
    $this->assertEquals((object)array('before' => true, 'after' => true), $this->executionTracker);
    
  }
  
  public function testPutDummyRequest()
  {
    
    //Reset the tracker here.
    $this->resetTracker();
    
    //Insert example dummy.
    $driver = $this->di->newInstance('Tuxion\DoctrineRest\Domain\Driver\DoctrineDriver');
    $body = array('title' => 'Testing 1, 2, 3...');
    $output = $driver->create($this->dummyEntity, $body);
    
    //Go through a request.
    $router = $this->router;
    $id = $output->getBody()->getId();
    $body = json_encode(array(
      'title' => 'Testing 7, 8, 9...'
    ));
    $response = $this->executeFakeRequest($router, 'PUT', '/rest/instantiation-dummy/'.$id, $body);
    
    //Assert the response.
    $expect = array(
      'status' => 'HTTP/1.1 200 OK',
      'body' => json_encode(array(
        'id' => 1,
        'title' => 'Testing 7, 8, 9...'
      ))
    );
    
    $this->assertEquals($expect, array(
      'status' => $response->status->get(),
      'body' => $response->content->get()
    ));
    
    $this->assertEquals((object)array('before' => true, 'after' => true), $this->executionTracker);
    
  }
  
  public function testDeleteDummyRequest()
  {
    
    //Reset the tracker here.
    $this->resetTracker();
    
    //Insert example dummy.
    $driver = $this->di->newInstance('Tuxion\DoctrineRest\Domain\Driver\DoctrineDriver');
    $body = array('title' => 'Testing 1, 2, 3...');
    $output = $driver->create($this->dummyEntity, $body);
    
    //Go through a request.
    $router = $this->router;
    $id = $output->getBody()->getId();
    $response = $this->executeFakeRequest($router, 'DELETE', '/rest/instantiation-dummy/'.$id);
    
    //Assert the response.
    $expect = array(
      'status' => 'HTTP/1.1 200 OK',
      'body' => ''
    );
    
    $this->assertEquals($expect, array(
      'status' => $response->status->get(),
      'body' => $response->content->get()
    ));
    
    $this->assertEquals((object)array('before' => true, 'after' => true), $this->executionTracker);
    
  }
  
  protected function generateRequest($method, $uri, $body='', $typeValue='application/json')
  {
    
    //Get the current request object.
    $request = $this->di->get('aura/web-kernel:request');
    
    //Fake the request method.
    $raw = new \ReflectionProperty(get_class($request->method), 'value');
    $raw->setAccessible(true);
    $raw->setValue($request->method, strtoupper($method));
    $raw->setAccessible(false);
    
    //Fake the request URL by replacing the URL object.
    $url = $this->di->newInstance('Aura\Web\Request\Url', array(
      'server' => array(
        'HTTP_HOST' => 'example.com',
        'REQUEST_URI' => $uri,
        'REQUEST_METHOD' => strtoupper($method)
      )
    ));
    
    $raw = new \ReflectionProperty(get_class($request), 'url');
    $raw->setAccessible(true);
    $raw->setValue($request, $url);
    $raw->setAccessible(false);
    
    //Insert the content body manually.
    $raw = new \ReflectionProperty(get_class($request->content), 'raw');
    $raw->setAccessible(true);
    $raw->setValue($request->content, $body);
    $raw->setAccessible(false);
    
    //Insert the content type manually.
    $raw = new \ReflectionProperty(get_class($request->content), 'type');
    $raw->setAccessible(true);
    $raw->setValue($request->content, $typeValue);
    $raw->setAccessible(false);
    
    return $request;
    
  }
  
  protected function generateRoutes()
  {
    
    $di = $this->di;
    $model = $this->dummyEntity;
    $name = 'instantiation-dummy';
    
    $mapper = $di->newInstance('Tuxion\DoctrineRest\Mapper\ResourceMapper', array(
      'routePrefix' => '/rest'
    ));
    
    $tracker = $this->executionTracker;
    $mapper->resource('*', $name, $model)
      ->before('*', function()use(&$tracker){$tracker->before = true;})
      ->after('*', function()use(&$tracker){$tracker->after = true;});
    
    return $mapper->getRouter();
    
  }
  
  protected function executeFakeRequest(Router $router, $method, $uri, $body='', $typeValue='application/json')
  {
    
    //Forge the request.
    $request = $this->generateRequest($method, $uri, $body, $typeValue);
    
    //Routing.
    $path = $request->url->get(PHP_URL_PATH);
    $route = $router->match($path, array(
      'HTTP_HOST' => 'example.com',
      'REQUEST_URI' => $uri,
      'REQUEST_METHOD' => strtoupper($method)
    ));
    
    //No route?
    if(!$route) throw new Exception("No route matched.");
    
    //Share route params that were matched.
    $request->params->set($route->params);
    
    //Dispatching.
    $action =$route->params['action'];
    $responder = $action();
    $responder();
    
    //Return the response.
    return $this->di->get('aura/web-kernel:response');
    
  }
  
  protected function resetTracker()
  {
    $this->executionTracker->before = false;
    $this->executionTracker->after = false;
  }
  
}