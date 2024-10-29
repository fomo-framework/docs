# Getting Started

## Introduction

Fomo is a web application framework based.

We tried to implement Fomo in the simplest possible way so that anyone can use it.

Fomo supports the following:

*   Simple, fast routing engine.
*   Processing work in the background.
*   Queued job processing.
*   And more...

Fomo is very fast, simple and for use in large scale projects.

## Why Fomo?

Here's 3 reason why you should use Fomo:

*   Fomo is very simple (less to learn and train others on).
*   Fomo is very fast (uses fewer resources to do the same thing).
*   Fomo is another tool that developers can use to solve their complex problems in a simple way.

## First Fomo Project

#### Tip

We have not tested Fomo on Windows. It may work just fine, but you may encounter problems installing and running Fomo. If there is a problem, send us the error message [in a Github issue](https://github.com/Fomo-framework/Fomo/issues).

Before creating your first Fomo project, you should ensure that your local machine has PHP, Composer, Swoole, Posix, Pcntl, and Redis installed.

  

After you have installed those dependencies, you may create a new Fomo project via the Composer create-project command:

```
composer create-project Fomo/Fomo example
```

After creating the project, start the Fomo development server using Mr. Engineer:

```
cd example

php engineer server:start
```

Once you have started the development server via the engineer command, your application will be accessible in your web browser at http://localhost:9000.

## Mr. Engineer

In Fomo, the boss is Mr. Engineer. In a funny, but serious way all orders are issued by Mr. Engineer to help keep your projects architecture consistent.

Commands like:

*   Start the HTTP server
*   Start the queue server
*   Start the scheduling server
*   Making controller, resource, middleware, service, job, task, etc...
*   Run tests
*   And more...

To see the commands that Mr. Engineer can issue You can run the following command

```
php engineer list
```

# The Basics

## Routing

tip: Routes are registered in routes/api.php

### # Basic Route

the most basic Fomo routes accept a URI and a callback, providing a very simple and expressive method of defining routes and behavior without complicated routing configuration files:

```php
<?php

namespace App\Controllers;

use Fomo\Request\Request;
use Fomo\Response\Response;

class TestController extends Controller
{
	public function getUsers(Request $request): Response
	{
		return response()->json([
			'message' => 'OK'
		]);
	}
}
```

tip: Note in getUsers method, if there is no mandatory parameter, the first entry can be deleted

```php
<?php

/** @var Fomo\Router\Router $router */

use App\Controllers\TestController;

$router->get('/users' , [TestController::class , 'getUsers']);
```

### # Available Router Methods

```php
$router->get($uri , $callback);
$router->post($uri , $callback);
$router->put($uri , $callback);
$router->patch($uri , $callback);
$router->delete($uri , $callback);
$router->head($uri , $callback);
$router->any($uri , $callback);
```

### # Route Parameters

#### tip

If you use parameters, note that it must be the first callback input of the Request class. And the parameters must be received exactly in the same order as specified in the uri (we will explain more later)

Sometimes you will need to capture segments of the URI within your route. For example, you may need to capture a user's ID from the URL. You may do so by defining route parameters:

```php
<?php

namespace App\Controllers;

use Fomo\Request\Request;
use Fomo\Response\Response;

class TestController extends Controller
{
	public function getUser(Request $request, int $id): Response
	{
		return response()->json([
			'userId' => $id
		]);
	}
}
```

```php
<?php

/** @var Fomo\Router\Router $router */

use App\Controllers\TestController;

$router->get('/users/{id}' , [TestController::class , 'getUser']);
```

### # Group

You can add a middleware or a prefix to several paths or delete a middleware for several paths

```php
$router->group([
	'middleware' => ['first' , 'second'] , // 'middleware' => 'first'
	'prefix' => 'admin' ,
	'withoutMiddleware' => 'first' ,
], function () use ($router) {
	//
	}
);
```

### # Middleware

To assign middleware to all routes within a group, you may use the middleware method before defining the group. Middleware are executed in the order they are listed in the array:

```php
$router->middleware(['first', 'second'])->group([] , function () use ($router) {
	//
});
```

### # Without Middleware

If the desired middleware exists in the path, it will be deleted

```php
$router->withoutMiddleware('first')->get($uri, $callback);
```

### # Prefix

The prefix method may be used to prefix each route in the group with a given URI. For example, you may want to prefix all route URIs within the group with admin:

```php
$router->prefix('admin')->group([] , function () use ($router) {
$router->get('users' , $callback); // Matches The "/admin/users" URL
});
```

## Controller

### # Make A controller

If you want to create a new controller, follow the command below

```
php engineer build:controller ControllerName
```

### # Basic Controller

Let's take a look at an example of a basic controller. Note that the controller extends the base controller class included with Fomo: App\\Controllers\\Controller:

```php
namespace App\Controllers;

use Fomo\Request\Request;
use Fomo\Response\Response;

class TestController extends Controller
{
	public function store(Request $request): Response
	{
		// insert data
		return response()->asNoContent();
	}
}
```

## Middleware

### # Make A Middleware

If you want to create a new middleware, follow the command below

```php
php engineer build:middleware MiddlewareName
```

### # Basic Middleware

Let's take a look at an example of a basic controller. Note that the output of the middleware should be 'true' if all operations are performed correctly, and otherwise it should be a Response class.

```php
namespace App\Middlewares;

use Fomo\Request\Request;
use Fomo\Response\Response;

class TestMiddleware
{
	public function handle(Request $request): bool|Response
	{
		if ($request->get('token') == 'my-secret-token'){
			return true;
		}

		return response()->json([
			'message' => 'ERROR'
		]);
	}
}
```

### # Assigning Middleware To Routes

If you want to assign the middleware to specific paths, you can use the following methods

```php
// Example 1
$router->middleware('first')->get($uri, $callback);

// Example 2
$router->middleware(FirstMiddleware::class)->get($uri, $callback);

// Example 3
$router->middleware(['first' , SecondMiddleware::class])->get($uri, $callback);

// Example 4
$router->group(['middleware' => 'first'] , function () use ($router){
//
});
```

### # Excluding Middleware

When assigning middleware to a group of routes, you may occasionally need to prevent the middleware from being applied to an individual route within the group. You may accomplish this using the withoutMiddleware method:

```php
// Example 1
$router->withoutMiddleware('first')->get($uri, $callback);

// Example 2
$router->withoutMiddleware(FirstMiddleware::class)->get($uri, $callback);

// Example 3
$router->withoutMiddleware(['first' , SecondMiddleware::class])->get($uri, $callback);

// Example 4
$router->group(['withoutMiddleware' => 'first'] , function () use ($router){
//
});
```

## Request

Fomo's Fomo\\Request\\Request class provides an object-oriented way to interact with the current HTTP request being handled by your application as well as retrieve the input that were submitted with the request.

### # Accessing The Request

To obtain an instance of the current HTTP request via dependency injection, you should type-hint the Fomo\\Request\\Request class on your route closure or controller method.

```php
namespace App\Controllers;

use Fomo\Request\Request;
use Fomo\Response\Response;

class UserController extends Controller
{
	public function store(Request $request): Response
	{
		$name = $request->input('name');

		//
	}
}
```

### # Retrieving The Request Path

The path method returns the request's path information. So, if the incoming request is targeted at http://example.com/foo/bar, the path method will return foo/bar:

```php
$uri = $request->path();
```

### # Retrieving The Request URL

To retrieve the full URL for the incoming request you may use the url or fullUrl methods. The url method will return the URL without the query string, while the fullUrl method includes the query string:

```php
$url = $request->url();

$urlWithQueryString = $request->fullUrl();
```

### # Retrieving The Request Method

The method method will return the HTTP verb for the request.

```php
$method = $request->method();
```

### # Headers

You may retrieve a request header from the Fomo\\Request\\Request instance using the header method. If the header is not present on the request, null will be returned. However, the header method accepts an optional second argument that will be returned if the header is not present on the request:

```php
$value = $request->header('X-Header-Name', 'default');
```

### # Retrieving Get Input Values

You can access data of type GET using the following method

```php
$name = $request->get('name', 'default');
```

When working with forms that contain array inputs, use "dot" notation to access the arrays. (Note that to activate this section, you must ENABLE the request index in the config/server.php file in the advanceMode presentation)

```php
$name = $request->get('products.0.name');

$names = $request->get('products.*.name');
```

### # Retrieving POST Input Values

You can access data of type POST using the following method

```php
$name = $request->post('name', 'default');
```

When working with forms that contain array inputs, use "dot" notation to access the arrays. (Note that to activate this section, you must ENABLE the request index in the config/server.php file in the advanceMode presentation)

```php
$name = $request->post('products.0.name');

$names = $request->post('products.*.name');
```

### # Retrieving Protocol Version

You can access the protocol version using the following method

```php
$protocolVersion = $request->protocolVersion();
```

### # Retrieving uri

You can access the uri using the following method

```php
$uri = $request->uri();
```

### # Retrieving queryString

You can access the queryString using the following method

```php
$queryString = $request->queryString();
```

### # Retrieving variable

You can access the path input parameters using the following method

```php
$queryString = $request->variable('id');
```

### # IP Address

The ip method may be used to retrieve the IP address of the client that made the request to your application:

```php
// example 1
$ipAddress = $request->remoteIp();

// example 2
$ipAddress = $request->ip();
```

### # Retrieving All Input Data

You may retrieve all of the incoming request's input data as an array using the all method. This method may be used regardless of whether the incoming request is from an HTML form or is an XHR request:

```php
$input = $request->all();
```

### # Retrieving An Input Value

Using a few simple methods, you may access all of the user input from your Fomo\\Request\\Request instance without worrying about which HTTP verb was used for the request. Regardless of the HTTP verb, the input method may be used to retrieve user input:

```php
$name = $request->input('name' , 'default');
```

When working with forms that contain array inputs, use "dot" notation to access the arrays. (Note that to activate this section, you must ENABLE the request index in the config/server.php file in the advanceMode presentation)

```php
$name = $request->input('products.0.name');

$names = $request->input('products.*.name');
```

### # Retrieving A Portion Of The Input Data

If you need to retrieve a subset of the input data, you may use the only and except methods. Both of these methods accept a single array or a dynamic list of arguments:

```php
$input = $request->only(['username', 'password']);

$input = $request->only('username', 'password');

$input = $request->except(['credit_card']);

$input = $request->except('credit_card');
```

## Response

Fomo\\Response\\Response class Fomo An object-oriented way to HTTP Response

### # response

Using the following method, you can create your response in any way you want

```php
namespace App\Controllers;

use Fomo\Request\Request;
use Fomo\Response\Response;

class UserController extends Controller
{
	public function store(Request $request): Response
	{
		return response(
			'message' ,
			200 ,
			[
				'Connection' => 'keep-alive' // default
			]
		);
	}
}
```

### # withHeader

Add a header to the response

```php
return response()->withHeader('name' , 'value');
```

### # withHeaders

Add some headers to the response

```php
return response()->withHeaders([
	'first' , 'value' ,
	'second' => 'value'
]);
```

### # withStatus

Set response status

```php
return response()->withStatus(400);
```

### # withBody

Set response message

```php
return response()->withBody('response message');
```

### # json

The json method will automatically set the Content-Type header to application/json, as well as convert the given array to JSON using the json\_encode PHP function:

```php
return response()->json([
	'name' => 'Abigail',
	'state' => 'CA',
]);
```

### # noContent

The noContent method will automatically set the Content-Type header to text/html; charset=utf-8, Content-Length header to 0 and also sets the value of the message equal to ''

```php
return response()->noContent();
```

### # asHtml

The html method will automatically set the Content-Type header to text/html; charset=utf-8, Content-Length header to strlen($html)

```php
return response()->html($html , 200);
```

## Validation

Fomo includes a wide variety of convenient validation rules that you may apply to data, even providing the ability to validate if values are unique in a given database table. We'll cover each of these validation rules in detail so that you are familiar with all of Fomo's validation features.

### # Validation Quickstart

Consider, we want to validate people's information:

#### routes/api.php

```php
/** @var Fomo\Router\Router $router */

$router->post('users' , [\App\Controllers\UserController::class , 'store']);
```

#### app/Controllers/UserController.php

```php
namespace App\Controllers;

use Fomo\Request\Request;
use Fomo\Response\Response;
use Fomo\Validation\Validation;

class UserController extends Controller
{
	public function store(Request $request): Response
	{
		$validate = new Validation($request->post() , [
			'name.firstName' => 'required|string|max:255|min:10',
			'name.lastName' => 'required|string|max:255|min:10',
			'age' => 'required|date:Y-m-d',
		]);

		if ($validate->hasError()){
			return response()->json([
				'errors' => $validate->getErrors()
			] , 422);
		}

		return response()->json([
			'message' => 'OK'
		]);
	}
}
```

### # hasError

The following method determines whether the information sent has errors or not

```php
if ($validate->hasError()){
//
}
```

### # hasMessage

The following method determines whether the information sent has errors or not

```php
if ($validate->hasMessage()){
//
}
```

### # getMessages

The following method returns an array of all error messages if there is an error, otherwise it returns an empty array.

```php
if ($validate->hasError()){
	return response()->json([
		'errorMessages' => $validate->getErrors()
	] , 422);
}
```

### # firstMessage

If there is an error, the following method returns only the first error message

```php
if ($validate->hasError()){
	return response()->json([
		'firstErrorMessage' => $validate->firstMessage()
	] , 422);
}
```

### # getErrors

If there is an error, the following method returns the complete information of that error (including the desired field of the error message only for each field and...)

```php
if ($validate->hasError()){
	return response()->json([
		'errors' => $validate->getErrors()
	] , 422);
}
```

### # firstErrors

If there is an error, the following method returns the complete information of the first error (including the desired field of the error message only for each field and...)

```php
if ($validate->hasError()){
	return response()->json([
		'firstError' => $validate->firstError()
	] , 422);
}
```

### # Available Validation Rules

###### # after:field

The field to be verified must have a value greater than the specified field

###### # before:field

The field to be verified must have a value less than the specified field

###### # required

The desired field must have a value and cannot be without a value

###### # string

The desired field must be of string type

###### # integer

The desired field must be of integer type

###### # boolean

The desired field must be of boolean type

###### # array

The desired field must be of array type

###### # email

The field under validation must be formatted as an email address.

###### # regex:pattern

The field under validation must match the given regular expression.

###### # notRegex:pattern

The field under validation must not match the given regular expression.

###### # max:value

The field under validation must be less than or equal to a maximum value. Strings, numerics, arrays, and files are evaluated in the same fashion as the size rule.

###### # min:value

The field under validation must have a minimum value. Strings, numerics, arrays, and files are evaluated in the same fashion as the size rule.

###### # size:value

The field under validation must have a size matching the given value. For string data, value corresponds to the number of characters. For numeric data, value corresponds to a given integer value (the attribute must also have the numeric or integer rule). For an array, size corresponds to the count of the array.

###### # date:format

The field under validation must be a valid, date according to the Carbon.

###### # in:foo,bar,...

The field under validation must be included in the given list of values.

###### # exists:table,column

The field under validation must exist in a given database table.

###### # unique:table,column

The field under validation must not exist within the given database table.

## Log

To help you learn more about what's happening within your application, Fomo provides robust logging services that allow you to log messages to files, the system error log to notify your entire team.

### # channel

Set log file name (default: Fomo)

```php
use Fomo\Log\Log;

Log::channel('security')->emergency('The system is down!');
```

### # Writing Log Messages

You may write information to the logs using the Log facade. As previously mentioned, the logger provides the eight logging levels defined in the RFC 5424 specification: emergency, alert, critical, error, warning, notice, info and debug:

```php
use Fomo\Log\Log;

Log::info($message, $content);
Log::alert($message, $content);
Log::critical($message, $content);
Log::debug($message, $content);
Log::emergency($message, $content);
Log::error($message, $content);
Log::log($message, $content);
Log::notice($message, $content);
Log::warning($message, $content);
```

# A Little Deeper

## Cache

Some of the data retrieval or processing tasks performed by your application could be CPU intensive or take several seconds to complete. When this is the case, it is common to cache the retrieved data for a time so it can be retrieved quickly on subsequent requests for the same data. The cached data is usually stored in a very fast data store such as Redis.

### # Cache Usage

  
  

###### # get instance of Cache

You can use the following method to get a cache sample

```php
use Fomo\Cache\Cache;

$cache = new Cache();
```

###### # Retrieving Items From The Cache

The Cache class's get method is used to retrieve items from the cache. If the item does not exist in the cache, null will be returned. If you wish, you may pass a second argument to the get method specifying the default value you wish to be returned if the item doesn't exist:

```php
$value = $cache->get('key');
```

You may even pass a closure as the default value. The result of the closure will be returned if the specified item does not exist in the cache. Passing a closure allows you to defer the retrieval of default values from a database or other external service:

```php
$value = $cache->get('key' , function () {
	return DB::table(/* ... */)->get();
});
```

###### # Checking For Item Existence

The has method may be used to determine if an item exists in the cache:

```php
if ($cache->has('key')) {
//
}
```

###### # Retrieve & Store

Sometimes you may wish to retrieve an item from the cache, but also store a default value if the requested item doesn't exist. For example, you may wish to retrieve all users from the cache or, if they don't exist, retrieve them from the database and add them to the cache. You may do this using the remember method:

```php
$value = $cache->remember('users', $seconds, function () {
	return DB::table('users')->get();
});
```

If the item does not exist in the cache, the closure passed to the remember method will be executed and its result will be placed in the cache.

  

You may use the rememberForever method to retrieve an item from the cache or store it forever if it does not exist:

```php
$value = $cache->rememberForever('users', $seconds, function () {
	return DB::table('users')->get();
});
```

###### # Retrieve & Delete

If you need to retrieve an item from the cache and then delete the item, you may use the pull method. Like the get method, null will be returned if the item does not exist in the cache:

```php
$value = $cache->pull('key');
```

###### # Storing Items In The Cache

You may use the put method on the Cache class to store items in the cache:

```php
$cache->put('key' , 'value' , $seconds = 10);
```

If the storage time is not passed to the put method, the item will be stored indefinitely:

```php
$cache->put('key' , 'value');
```

## Helpers

Fomo includes a variety of global helper PHP functions. Many of these functions are used by the framework itself; however, you are free to use them in your own applications if you find them convenient.

### Application Helpers

#### # basePath(string $path = null)

The basePath function returns the fully qualified path to your application's root directory. You may also use the basePath function to generate a fully qualified path to a given file relative to the project root directory:

```php
// pulls from PROJECT_PATH
// PROJECT_PATH is defined in `engineer` as `realpath('./');`
// Ex: /home/user/projects/my-project/ as directory
$path = basePath();
// Returns: /home/user/projects/my-project/
// Note: There is a trailing slash

$path = basePath('vendor/bin');
// Returns: /home/user/projects/my-project/vendor/bin
// Note: If you don't define a trailing slash, there won't be one!
```

#### # appPath(string $path = null)

The appPath function returns the fully qualified path to your application's app directory. You may also use the appPath function to generate a fully qualified path to a file relative to the application directory:

```php
// Ex: /home/user/projects/my-project/ as directory
$path = appPath();
// Returns: /home/user/projects/my-project/app/

$path = appPath('Controllers/Controller.php');
// Returns: /home/user/projects/my-project/app/Controllers/Controller.php
```


#### # configPath(string $path = null)

The configPath function returns the fully qualified path to your application's config directory. You may also use the configPath function to generate a fully qualified path to a given file within the application's configuration directory:

```php
// Ex: /home/user/projects/my-project/ as directory
$path = configPath();
// Returns: /home/user/projects/my-project/config/

$path = configPath('app.php');
// Returns: /home/user/projects/my-project/config/app.php
```

#### # databasePath(string $path = null)

The databasePath function returns the fully qualified path to your application's database directory. You may also use the databasePath function to generate a fully qualified path to a given file within the database directory:

```php
// Ex: /home/user/projects/my-project/ as directory
$path = databasePath();
// Returns: /home/user/projects/my-project/database/

$path = databasePath('Factory.php');
// Returns: /home/user/projects/my-project/database/Factory.php
```

#### # languagePath(string $path = null)

The languagePath function returns the fully qualified path to your application's language directory. You may also use the languagePath function to generate a fully qualified path to a given file within the directory:

```php
// Ex: /home/user/projects/my-project/ as directory
$path = languagePath();
// Returns: /home/user/projects/my-project/language/

$path = languagePath('validation/en');
// Returns: /home/user/projects/my-project/language/validation/en
```

#### # storagePath(string $path = null)

The storagePath function returns the fully qualified path to your application's storage directory. You may also use the storagePath function to generate a fully qualified path to a given file within the directory:

```php
// Ex: /home/user/projects/my-project/ as directory
$path = storagePath();
// Returns: /home/user/projects/my-project/storage/

$path = storagePath('logs/Fomo.log');
// Returns: /home/user/projects/my-project/storage/logs/Fomo.log
```

#### # config(string $key, $default = null)

The config function gets the value of a configuration variable. The configuration values may be accessed using "dot" syntax, which includes the name of the file and the option you wish to access. A default value may be specified and is returned if the configuration option does not exist:

```php
// from app/config/app.php
return [
	'timezone' => 'UTC',
];

// When pulling the value within your application
$value = config('app.timezone');
// Returns: UTC

$default = 'GMT';
$value = config('app.timezone', $default);
// Returns: UTC, but if the value is not set, it will return GMT
```

#### # request()

The request function returns an instance of the current request from the request factory:

```php
// Full request object
$request = request();

// URL Path
$path = request()->path();

// IP
$ip = request()->ip();

// Get a URL parameter
// Ex: example.com/users/1?name=John
$name = request()->get('name');
// Returns; John
```

#### # response()

The response function creates a response instance or obtains an instance of the response factory:

```php
$html = '<html><body><h1>Hello, World!</h1></body></html>';
$statusCode = 200;
// Automatically sets the Content-Type header to text/html; charset=utf-8
return response()->html($html, $statusCode);

// Automatically sets the Content-Type header to application/json
return response()->json(['foo' => 'bar'], 200);

// or with custom headers
return response()
	->withHeaders(['X-Header' => 'Value'])
	->json(['foo' => 'bar'], 200);
```

#### # auth()

The auth function returns an authenticator instance. You may use it as an alternative to the `Auth` class:

```php
$user = auth()->user();
```

#### # elasticsearch()

The elasticsearch function returns an elasticsearch instance. You may use it as a shortcut to the Elasticsearch class:

```php
$searchResults = elasticsearch()->msearch(/* set your query */);
```

#### # redis()

The redis function returns an redis instance. You may use it as an alternative to the Redis class:

```php
$myCachedRedisValue = redis()->get(/*get your key */);
```

#### # mail()

The mail function returns an mail instance. You may use it as an alternative to the Mail class:

```php
mail()->body(/*set your body */)->send();
```

#### # cache()

The cache function returns a cache instance. You may use it as an alternative to the Cache class:

```php
$myCachedValue = cache()->get(/*get your key */);
```

#### # validation()

The validation function returns a validation instance. You may use it as an alternative to the Validation class:

```php
$hasError = validation($myValidationData, $myRules)->hasError();
```


#### # env(mixed $key, mixed $default = null)

The env function retrieves the value of an environment variable or returns a default value:

```php
$env = env('APP_ENV');

$env = env('APP_ENV', 'production');
```

### Swoole Helpers

#### # cpuCount()

The cpuCount function returns the number of CPU cores available on the server:

```php
$cores = cpuCount();
```

#### # getMasterProcessId()

The getMasterProcessId function returns the master process ID:

```php
$masterId = getMasterProcessId();
```

#### # getWorkerProcessIds()

The getWorkerProcessIds function returns an array of worker process IDs:

```php
$workerIds = getWorkerProcessIds();
```

#### # getManagerProcessId()

The getManagerProcessId function returns the manager process ID:

```php
$managerId = getManagerProcessId();
```

#### # getWatcherProcessId()

The getWatcherProcessId function returns the watcher process ID:

```php
$watcherId = getWatcherProcessId();
```

#### # getFactoryProcessId()

The getFactoryProcessId function returns the factory process ID:

```php
$factoryId = getFactoryProcessId();
```

#### # getQueueProcessId()

The getQueueProcessId function returns the queue process ID:

```php
$queueId = getQueueProcessId();
```

#### # getSchedulingProcessId()

The getSchedulingProcessId function returns the scheduling process ID:

```php
$schedulingId = getSchedulingProcessId();
```

#### # httpServerIsRunning()

The httpServerIsRunning function returns true if the HTTP server is running:

```php
$is_running = httpServerIsRunning();
```

#### # queueServerIsRunning()

The queueServerIsRunning function returns true if the queue server is running:

```php
$is_running = queueServerIsRunning();
```

#### # schedulingServerIsRunning()

The schedulingServerIsRunning function returns true if the scheduling server is running:

```php
$is_running = schedulingServerIsRunning();
```

# Server

## HTTP Client

Fomo provides an expressive, minimal API around the Guzzle HTTP client, allowing you to quickly make outgoing HTTP requests to communicate with other web applications. Fomo's wrapper around Guzzle is focused on its most common use cases and a wonderful developer experience.

### # Making Requests

To make requests, you may use the head, get, post, headput, patch, and delete methods provided by the Http facade. First, let's examine how to make a basic GET request to another URL:

```php
use Fomo\Http\Http;

$http = new Http();
$response = $http->get('http://example.com');
```

The get method returns an instance of Fomo\\Http\\Response, which provides a variety of methods that may be used to inspect the response:

```php
$response->body() : string;
$response->json($key = null) : array|mixed;
$response->object() : object;
$response->status() : int;
$response->ok() : bool;
$response->successful() : bool;
$response->redirect(): bool;
$response->failed() : bool;
$response->serverError() : bool;
$response->clientError() : bool;
$response->header($header) : string;
$response->headers() : array;
```

### # Request Data

Of course, it is common when making POST, PUT, and PATCH requests to send additional data with your request, so these methods accept an array of data as their second argument. By default, data will be sent using the application/json content type:

```php
$response = $http->post('http://example.com', [
	'foo' => 'bar'
]);
```

### # GET Request Query Parameters

When making GET requests, you may either append a query string to the URL directly or pass an array of key / value pairs as the second argument to the get method:

```php
$response = $http->get('http://example.com', [
	'foo' => 'bar'
]);
```

### # Sending Form URL Encoded Requests

If you would like to send data using the application/x-www-form-urlencoded content type, you should call the asForm method before making your request:

```php
$response = $http->asForm()->post('http://example.com', [
	'foo' => 'bar'
]);
```

### # Sending A Raw Request Body

You may use the withBody method if you would like to provide a raw request body when making a request. The content type may be provided via the method's second argument:

```php
$response = $http->withBody(
	base64_encode($photo), 'image/jpeg'
)->post('http://example.com');
```

### # Multi-Part Requests

If you would like to send files as multi-part requests, you should call the attach method before making your request. This method accepts the name of the file and its contents. If needed, you may provide a third argument which will be considered the file's filename:

```php
$response = $http->attach(
	'attachment', file_get_contents('photo.jpg'), 'photo.jpg'
)->post('http://example.com');
```

Instead of passing the raw contents of a file, you may pass a stream resource:

```php
$photo = fopen('photo.jpg', 'r');
$response = $http->attach(
	'attachment', $photo, 'photo.jpg'
)->post('http://example.com');
```

### # Headers

Headers may be added to requests using the withHeaders method. This withHeaders method accepts an array of key / value pairs:

```php
$response = $http->withHeaders([
	'X-First' => 'foo',
	'X-Second' => 'bar'
])->post('http://example.com', [
	'foo' => 'bar',
]);
```

### # Error Handling

Unlike Guzzle's default behavior, Fomo's HTTP client wrapper does not throw exceptions on client or server errors (400 and 500 level responses from servers). You may determine if one of these errors was returned using the isSuccessfulsuccessful, isClientError, or isServerError methods:

```php
// Determine if the status code is >= 200 and < 300...
$response->isSuccessful();

// Determine if the status code is >= 400...
$response->isFailed();

// Determine if the response has a 400 level status code...
$response->isClientError();

// Determine if the response has a 500 level status code...
$response->isServerError();

// Immediately execute the given callback if there was a client or server error...
$response->onError(callable $callback);

```

## HTTP Server

An HTTP server is a computer (software) program (or even a software component included in an other program) that plays the role of a server in a client–server model by implementing the server part of the HTTP and/or HTTPS network protocol(s). An HTTP server waits for the incoming client requests (sent by user agents like browsers, web crawlers, etc.) and for each request it answers by replying with requested information, including the sending of the requested web resource, or with an HTTP error message.

### # start command

You can use the following command to start the server

```bash
php engineer server:start
```

With the options \--watch or \-w, you can give this command to the server, so that if there are changes in the files, the server will automatically reload in real time.

```bash
php engineer server:start -w
# or
php engineer server:start --watch
```

With the options \--daemonize or \-d, you can give this command to the server, so that the server runs in the background

```bash
php engineer server:start -d
//or
php engineer server:start --daemonize
```

### # reload command

You can reload the server with the following command

```bash
php engineer server:reload
```

### # status command

You can check the status of all processes with the following command

```bash
php engineer server:status
```

### # stop command

You can stop the server with the following command

```bash
php engineer server:stop
```

## Queue Server

While building your web application, you may have some tasks, such as parsing and storing an uploaded CSV file, that take too long to perform during a typical web request. Thankfully, Fomo allows you to easily create queued jobs that may be processed in the background. By moving time intensive tasks to a queue, your application can respond to web requests with blazing speed and provide a better user experience to your customers.

Queues in Fomo are supported by redis

### # start command

You can use the following command to start the server

```bash
php engineer queue:start
```

### # status command

You can check the status of all processes with the following command

```bash
php engineer queue:status
```

### # stop command

You can stop the server with the following command

```bash
php engineer queue:stop
```

### # Generating Job Classes

By default, all of the queueable jobs for your application are stored in the app/Jobs directory. If the app/Jobs directory doesn't exist, it will be created when you run the build:job engineer command:

```bash
php engineer build:job JobName
```

### # Class Structure

Job classes are very simple, normally containing only a handle method that is invoked when the job is processed by the queue. To get started, let's take a look at an example job class. In this example, we'll pretend we manage a podcast publishing service and need to process the uploaded podcast files before they are published:

```php
namespace App\Jobs;

use Fomo\Job\DispatchTrait;

class ProcessPodcast
{
	use DispatchTrait;

	public $podcast;

	public function __construct(Podcast $podcast)
	{
		$this->podcast = $podcast;
	}

	public function handle()
	{
		// Process podcast...
	}
}
```

The handle method is invoked when the job is processed by the queue.

### # Dispatching Jobs

Once you have written your job class, you may dispatch it using the dispatch method on the job itself. The arguments passed to the dispatch method will be given to the job's constructor:

```php
namespace App\Http\Controllers;

use App\Jobs\ProcessPodcast;

class PodcastController extends Controller
{
	public function store(Request $request)
	{
		$podcast = DB::create(/* ... */);

		// ...

		ProcessPodcast::dispatch($podcast);
	}
}
```

## Scheduling Server

In the past, you may have written a cron configuration entry for every task you needed to schedule on your server. However, this can quickly become a pain because your job application is no longer in source control and you need to SSH into your server to view existing cron entries or add additional entries.

Fomo Command Scheduler offers a new approach to managing scheduled tasks on your server. The scheduler allows you to smoothly and clearly define the schedule of your commands in the Fomo app itself. When using scheduler, you don't need any cron entry on your server and all tasks are done through Fomo. The scheduling of your tasks is defined in the tasks method of the app/Scheduling/Kernel.php file. To help you get started, a simple example is defined inside the method.

### # start command

You can use the following command to start the server

```bash
php engineer scheduling:start
```

### # status command

You can check the status of all processes with the following command

```bash
php engineer scheduling:status
```

### # stop command

You can stop the server with the following command

```bash
php engineer scheduling:stop
```

### # Generating Task Classes

By default, all of the scheduling jobs for your application are stored in the app/Scheduling/Tasks directory. If the app/Scheduling/Tasks directory doesn't exist, it will be created when you run the build:task engineer command:

```bash
php engineer build:task TaskName
```

### # Class Structure

Task classes are very simple, usually containing only a handle method that is called by the scheduler when the task is processed.

```php
<?php

namespace App\Scheduling\Tasks;

use Fomo\Database\DB;

class ClearTableTask
{
	public function handle(): void
	{
		// Process
	}
}
```

### # Defining Schedules

You may define all of your scheduled tasks in the schedule method of your application's App\\Scheduling\\Kernel class. To get started, let's take a look at an example. In this example, we will schedule a closure to be called every day at midnight. Within the closure we will execute a database query to clear a table:

  

###### app/Scheduling/Tasks/ClearTableTask.php

```php
<?php

namespace App\Scheduling\Tasks;

use Fomo\Database\DB;

class ClearTableTask
{
	public function handle(): void
	{
		DB::table('recent_users')->delete();
	}
}
```

  

###### app/Scheduling/Kernel.php

```php
<?php

namespace App\Scheduling;

use App\Scheduling\Tasks\ClearTableTask;
use Fomo\Scheduling\Scheduler;

class Kernel
{
	public function tasks(): void
	{
		(new Scheduler())->call(ClearTableTask::class)->daily();
	}
}
```

### # Schedule Frequency Options

We've already seen a few examples of how you may configure a task to run at specified intervals. However, there are many more task schedule frequencies that you may assign to a task:

  

| Method | Description |
| --- | --- |
| `->cron('* * * * *');` | Run the task on a custom cron schedule |
| `->everyMinute();` | Run the task every minute |
| `->everyTwoMinutes();` | Run the task every two minutes |
| `->everyThreeMinutes();` | Run the task every three minutes |
| `->everyFourMinutes();` | Run the task every four minutes |
| `->everyFiveMinutes();` | Run the task every five minutes |
| `->everyTenMinutes();` | Run the task every ten minutes |
| `->everyFifteenMinutes();` | Run the task every fifteen minutes |
| `->everyThirtyMinutes();` | Run the task every thirty minutes |
| `->hourly();` | Run the task every hour |
| `->hourlyAt(17);` | Run the task every hour at 17 minutes past the hour |
| `->everyOddHour();` | Run the task every odd hour |
| `->everyTwoHours();` | Run the task every two hours |
| `->everyThreeHours();` | Run the task every three hours |
| `->everyFourHours();` | Run the task every four hours |
| `->everySixHours();` | Run the task every six hours |
| `->daily();` | Run the task every day at midnight |
| `->dailyAt('13:00');` | Run the task every day at 13:00 |
| `->twiceDaily(1, 13);` | Run the task daily at 1:00 & 13:00 |
| `->twiceDailyAt(1, 13, 15);` | Run the task daily at 1:15 & 13:15 |
| `->weekly();` | Run the task every Sunday at 00:00 |
| `->weeklyOn(1, '8:00');` | Run the task every week on Monday at 8:00 |
| `->monthly();` | Run the task on the first day of every month at 00:00 |
| `->monthlyOn(4, '15:00');` | Run the task every month on the 4th at 15:00 |
| `->twiceMonthly(1, 16, '13:00');` | Run the task monthly on the 1st and 16th at 13:00 |
| `->lastDayOfMonth('15:00');` | Run the task on the last day of the month at 15:00 |
| `->quarterly();` | Run the task on the first day of every quarter at 00:00 |
| `->yearly();` | Run the task on the first day of every year at 00:00 |
| `->yearlyOn(6, 1, '17:00');` | Run the task every year on June 1st at 17:00 |