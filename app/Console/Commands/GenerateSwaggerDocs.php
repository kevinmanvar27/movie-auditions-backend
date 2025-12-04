<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class GenerateSwaggerDocs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swagger:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Swagger documentation for the API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Generating Swagger documentation...');
        
        // Run the OpenApi command to generate documentation
        $process = new Process([
            'php',
            base_path('vendor/bin/openapi'),
            base_path('app/Http/Controllers/API'),
            '--output',
            public_path('swagger.json')
        ]);
        
        try {
            $process->mustRun();
            $this->info('Swagger documentation generated successfully!');
            $this->line($process->getOutput());
        } catch (ProcessFailedException $exception) {
            $this->error('Failed to generate Swagger documentation:');
            $this->error($exception->getMessage());
            return 1;
        }
        
        return 0;
    }
}