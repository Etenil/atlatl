ATLATL
======

VERSION 0.1
UPDATED 29 May 2012


Introduction
------------
PHP is a simple and fast interpreted language and templating engine
for the web. Its ubiquity make it a language to be reckoned
with. However, it is technically lacking in many
respects. Particularly on the semantic consistency and security.

Thus, many frameworks, collections of libraries, appeared. Those
quickly became very popular, and they grew tremendously. So much so
that frameworks have started to pull their components apart, and
provide a clean and lean core, called a *micro-framework*, along
with a large middleware layer that turn the *micro-framework*
into the full-blown framework.

Atlatl is one such micro-framework. It is the core of Assegai, a
larger and more structured framework. This manual documents only
Atlatl and how to create web applications with it.


Hello World
-----------
This chapter gives a practical approach to creating a simple
application with Atlatl.

First of all, you need to download and install Atlatl onto your
machine. Clone the Atlatl repository at
http://pikacode.com/etenil/atlatl
with mercurial.

Atlatl does not dictate a particular project structure nor to
have your source files anywhere in particular. All you need to
do is to include or require **loader.php**
within the Atlatl folder.

In order to respond to requests, you will need to create a
controller that extends
**atlatl\Controller**, a blank implementation
of a controller.

The controller's methods will be routed to following a routing
table that you need to define.

Then, you can instanciate an **atlatl\Core**
object and call the **serve** method on it,
passing the routing table as parameter.

    require('atlatl/loader.php');

    class Hello extends atlatl\Controller
    {
        function greet()
        {
            return "Hello, world";
        }
    }

    $route = array(
        '/' => 'Hello::greet',
        );

    $srv = new atlatl\Core();
    $srv->serve($route);

Routes are regular expressions, and we can capture parts of them
to pass them to handling methods. Thus, we can easily create a
custom greeter like in the following example.

    require('atlatl/loader.php');

    class Hello extends atlatl\Controller
    {
        function greet()
        {
            return "Hello, world";
        }

        function greetPerson($name)
        {
            return "Hello, " . $name;
        }
    }

    $route = array(
        '/' => 'Hello::greet',
        '/(.+)' => 'Hello::greetPerson',
        );

    $srv = new atlatl\Core();
    $srv->serve($route);


Routes
------
### Definition

Routes are defined by the association of a regular expression
to the definition of a controller's method. When a request
that matches the route is received, the controller is
instantiated and its method called.

Routes definitions may use capturing groups. The matches are
mapped on to the controller's method's parameters directly.

Routes don't necessarily need to point to object's methods
that extend **atlatl\Controller**, but it
is advised to use those.


### Method

The routing can be done either for all types of requests, or
based on the request type. Request types can be any of those
supported in the HTTP specification.

Specifying a route method is just a matter of prepending the
method type to the route with a colon.

    $route = array(
        '/'      => 'Controller::method',
        'GET:/'  => 'Controller::method',
        'POST:/' => 'Controller::method',
    );

Requests will be handled by their method-specific route if
defined. If a method-specific route cannot be found, then the
generic route definition will be used.


Handlers
--------
Atlatl does not provide any strict definition of a handler. You can
use any construct that can be called upon.

A handler can be a closure, a function's name, an array with a
classname as first element and the method to call as second element,
or a method definition string such as **class::method**.

If the handler is an object's method, Atlatl will instanciate the
object for you. The constructor is then called with the three
following arguments.

- modules
- server
- request
- sec

However, if the handler is a function or a closure, the first
parameter passed is an object with public properties as previously
described in the parameters list.

Handlers are expected to either return a
**string** or an
**atlatl\Response** object.


### Request handling hooks

When routing the request, the core routine will call the two methods
**preRequest** and **postRequest** if they exist. Respectively
before and after routing the request.

The **preRequest** function does not take any
argument and is not expected to return any value. It is useful
to set up properties of the controller or sort out some side
effects before handling the request.

The other hook, **postRequest** takes a
*mixed* parameter that is the value returned by the
handler. The hook is expected to return a
**atlatl\Response** object or a string that
will then be sent back to the client.


### Helpers

Handlers are passed the three helpers
**atlalt\Request**, **atlatl\Server**, **atlatl\Security**
and **atlatl\ModuleContainer**.

They are instanciated as properties of the current container
using with the following self-explanatory names:

- modules
- server
- request
- sec

Refer to their respective chapters for more information.


Server Information
------------------
Atlatl abstracts all server information into a
**atlatl\Server** object. An instance of this
object is provided when a controller is instanciated. It is
accessible within controllers as a property.

The object provides read-only access to the server's
parameters. These are parsed from the global
**$_SERVER** variable by default. Those variables
THAT ARE SEparated by specific characters are automatically
split into arrays.


Modules
-------
### Using modules

Loaded modules are accessible from the controller as a
**atlatl\ModuleContainer** property. many
different actions can be performed on individual modules;
refer to the modules documentation for more details.

The **atlatl\ModuleContainer** object
offers the possibility to run a generic method on all
instanciated modules.

    class Stuff extends atlatl\Controller
    {
        function myHandler()
        {
            // Retrieving data from the pdo module.
            $table = $this->modules->pdo->query('SELECT * FROM data');

            // Closing all database connections.
            $this->modules->runMethod('close');
        }
    }


### Writing new modules

Modules are a convenient way to extend the framework's
functionalities. They allow for the core's code to be small
and maintainable while still bringing in all the features that
one could desire.

Modules are objects that extend the standard
**atlatl\Module** implementation. They come
with standard methods that are called by hooks within Atlatl's
core and can change its behaviour.

- _init($options) method called by the constructor. Overload this with custom code instead of the constructor.
- preRouting($path, $route, Request $request) method called before the routing algorithm is started.
- postRouting($path, $route, Request $request, Response $response) called after the routing was done.
- preView(Request $request, $path, $vars) called before displaying a view.
- postView(Request $request, $path, $vars) called after displaying a view.
