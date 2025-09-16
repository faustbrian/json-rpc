<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\JsonRpc\Http\Middleware\BootServer;
use Cline\JsonRpc\Http\Middleware\ForceJson;
use Cline\OpenRpc\ContentDescriptor\CursorPaginatorContentDescriptor;
use Cline\OpenRpc\Schema\CursorPaginatorSchema;
use Illuminate\Routing\Middleware\SubstituteBindings;

return [
    /*
    |--------------------------------------------------------------------------
    | Namespace Configuration
    |--------------------------------------------------------------------------
    |
    | This section defines the namespaces used within the application. These
    | namespaces are used to autoload important classes, such as the methods
    | namespace where your server methods are defined.
    |
    */

    'namespaces' => [
        /*
        |--------------------------------------------------------------------------
        | Methods Namespace
        |--------------------------------------------------------------------------
        |
        | This is the namespace to the directory where your server methods are
        | defined. By default, it is set to 'App\Http\Methods'. You can change
        | this to any namespace that suits your application's structure.
        |
        */

        'methods' => 'App\\Http\\Methods',
    ],
    /*
    |--------------------------------------------------------------------------
    | Path Configuration
    |--------------------------------------------------------------------------
    |
    | This section defines the paths used within the application. These paths
    | are used to locate important directories and files, such as the methods
    | directory where your server methods are defined.
    |
    */

    'paths' => [
        /*
        |--------------------------------------------------------------------------
        | Methods Path
        |--------------------------------------------------------------------------
        |
        | This is the path to the directory where your server methods are defined.
        | By default, it is set to 'app/Http/Methods'. You can change this to
        | any directory that suits your application's structure.
        |
        */

        'methods' => app_path('Http/Methods'),
    ],
    /*
    |--------------------------------------------------------------------------
    | Resources Configuration
    |--------------------------------------------------------------------------
    |
    | This section defines the resources used within the application. Resources
    | provide a transformation layer between your data models and the JSON
    | responses returned to clients. This allows for the customization of the
    | JSON representation of data, such as selectively displaying attributes
    | or always including specific relationships. Utilize resource classes to
    | transform models and collections into JSON with ease and flexibility.
    |
    */

    'resources' => [
        //
    ],
    /*
    |--------------------------------------------------------------------------
    | Server Configuration
    |--------------------------------------------------------------------------
    |
    | This section defines the server configuration for the application. You
    | can specify multiple servers, each with its own settings such as name,
    | path, version, middleware, methods, and schemas. This setup allows you
    | to create and manage multiple endpoints within the same application.
    |
    */

    'servers' => [
        [
            /*
            |--------------------------------------------------------------------------
            | Server Name
            |--------------------------------------------------------------------------
            |
            | The name of the server, which is used to identify the server instance.
            | If not specified, it defaults to the value of the APP_NAME environment
            | variable, ensuring a fallback to the application's name.
            |
            */

            'name' => env('APP_NAME'),
            /*
            |--------------------------------------------------------------------------
            | Server Path
            |--------------------------------------------------------------------------
            |
            | The URI path where the server will be accessible. This defines the
            | endpoint through which the server can be reached. Modify this path
            | to suit the routing needs of your application.
            |
            */

            'path' => '/rpc',
            /*
            |--------------------------------------------------------------------------
            | Server Route
            |--------------------------------------------------------------------------
            |
            | The name of the route for the server. This is used internally to
            | reference the server route configuration. It should be unique within
            | your application to avoid conflicts with other routes.
            |
            */

            'route' => 'rpc',
            /*
            |--------------------------------------------------------------------------
            | Server Version
            |--------------------------------------------------------------------------
            |
            | The version of the server. This helps in versioning your API endpoints.
            | Update this value as needed to reflect changes or upgrades in your
            | server's API, allowing clients to request specific versions.
            |
            */

            'version' => '1.0.0',
            /*
            |--------------------------------------------------------------------------
            | Server Middleware
            |--------------------------------------------------------------------------
            |
            | Middleware are used to filter HTTP requests entering your application.
            | These middleware will be applied to the server routes, allowing you to
            | add custom logic such as authentication, logging, or input modification.
            | 'ForceJson::class' ensures all responses are in JSON format, while
            | 'BootServer::class' might initialize certain server settings or checks.
            |
            */

            'middleware' => [
                SubstituteBindings::class,
                'auth:sanctum',
                ForceJson::class,
                BootServer::class,
            ],
            /*
            |--------------------------------------------------------------------------
            | Server Methods
            |--------------------------------------------------------------------------
            |
            | The methods that the server will expose. These are the functions or
            | operations that clients can call. If set to null, all available
            | methods will be exposed. Otherwise, specify an array of method
            | names to restrict the exposed methods.
            |
            */

            'methods' => null,
            /*
            |--------------------------------------------------------------------------
            | OpenRPC Content Descriptors
            |--------------------------------------------------------------------------
            |
            | The OpenRPC content descriptors define the structure and attributes
            | of the inputs and outputs for the server's methods. This helps in
            | generating accurate API documentation and ensures that clients
            | understand the expected data formats.
            |
            */

            'content_descriptors' => [
                CursorPaginatorContentDescriptor::create(),
            ],
            /*
            |--------------------------------------------------------------------------
            | JSON Schemas
            |--------------------------------------------------------------------------
            |
            | The JSON Schemas for the server. These schemas are used to validate
            | the data structures that your server methods will accept or return.
            | This ensures data integrity and helps maintain a consistent API
            | contract with your clients.
            |
            */

            'schemas' => [
                CursorPaginatorSchema::create(),
            ],
        ],
    ],
];
