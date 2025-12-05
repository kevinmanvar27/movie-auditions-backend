<?php

require_once 'vendor/autoload.php';

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

// Create the application container
$app = new Application(realpath(__DIR__));

// Bind important interfaces to the container
$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

// Set the application as the Facade root
Facade::setFacadeApplication($app);

// Bootstrap the application
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

// Load the User model
require_once 'app/Models/User.php';

// Try to create a token for the first user
$user = App\Models\User::first();
if ($user) {
    $token = $user->createToken('test-token');
    echo "Token created successfully: " . $token->plainTextToken . "\n";
} else {
    echo "No user found in the database.\n";
}