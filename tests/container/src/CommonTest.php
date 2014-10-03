<?php namespace Tuxion\DoctrineRest\_Config;

use \Exception;
use Aura\Web\Request;
use Aura\Router\Router;
use Aura\Di\_Config\AbstractContainerTest;

class CommonTest extends AbstractContainerTest
{
  
  protected $dummyEntity = 'Tuxion\DoctrineRest\Domain\Dummy\DummyEntity';
  
  protected function getConfigClasses()
  {
    return array(
      'Aura\Web\_Config\Common',
      'Tuxion\DoctrineRest\_Config\Test', //This is for including our required stubs.
      'Tuxion\DoctrineRest\_Config\Common',
    );
  }
  
  public function provideGet()
  {
    
    //Do this to silence the skip.
    return array(
      array('doctrine/orm:entity-manager', 'Doctrine\ORM\EntityManager')
    );
    
  }
  
  public function provideNewInstance()
  {
    return array(
      array('Tuxion\DoctrineRest\Action\ActionFactory'),
      array('Tuxion\DoctrineRest\Domain\Driver\DoctrineDriver'),
      array('Tuxion\DoctrineRest\Domain\Result\ResultFactory'),
      array('Tuxion\DoctrineRest\Responder\RestResponder'),
      array('Tuxion\DoctrineRest\Responder\StatusCodes'),
      array('Tuxion\DoctrineRest\RouteAttacher', array(
        'model' => $this->dummyEntity,
        'resource' => 'instantiation-dummy'
      ))
    );
  }
  
  public function testGetDummyRequest()
  {
    
    //Insert example dummy.
    $driver = $this->di->newInstance('Tuxion\DoctrineRest\Domain\Driver\DoctrineDriver');
    $body = array('title' => 'Testing 1, 2, 3...');
    $output = $driver->create($this->dummyEntity, $body);
    
    //Go through a request.
    $router = $this->generateRoutes();
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
    
    
  }
  
  public function testPostDummyRequest()
  {
    
    //Go through a request.
    $router = $this->generateRoutes();
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
    
  }
  
  public function testPutDummyRequest()
  {
    
    //Insert example dummy.
    $driver = $this->di->newInstance('Tuxion\DoctrineRest\Domain\Driver\DoctrineDriver');
    $body = array('title' => 'Testing 1, 2, 3...');
    $output = $driver->create($this->dummyEntity, $body);
    
    //Go through a request.
    $router = $this->generateRoutes();
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
    
  }
  
  public function testDeleteDummyRequest()
  {
    
    //Insert example dummy.
    $driver = $this->di->newInstance('Tuxion\DoctrineRest\Domain\Driver\DoctrineDriver');
    $body = array('title' => 'Testing 1, 2, 3...');
    $output = $driver->create($this->dummyEntity, $body);
    
    //Go through a request.
    $router = $this->generateRoutes();
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
  
  protected function generateRoutes($prefix='rest')
  {
    
    $di = $this->di;
    $router = $di->newInstance('Aura\Router\Router');
    $router->attach($prefix, "/$prefix", function($router)use($di){
      
      $name = 'instantiation-dummy';
      $attacher = $di->newInstance('Tuxion\DoctrineRest\RouteAttacher', array(
        'model' => $this->dummyEntity,
        'resource' => $name
      ));
      $router->attach($name, "/$name", $attacher);
      
    });
    
    return $router;
    
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
  
}