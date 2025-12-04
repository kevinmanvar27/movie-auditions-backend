#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use OpenApi\Generator;
use OpenApi\Util;

// Generate the OpenAPI documentation
$openapi = Generator::scan([
    __DIR__.'/app/Http/Controllers/API'
]);

// Save to public/swagger.json
file_put_contents(__DIR__.'/public/swagger.json', $openapi->toJson());

echo "Swagger documentation generated successfully!\n";