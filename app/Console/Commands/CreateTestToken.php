<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CreateTestToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-test-token {userId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a personal access token for testing Sanctum';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('userId') ?? 1;
        
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }
        
        $token = $user->createToken('test-token');
        
        $this->info("Token created successfully for user {$user->name}:");
        $this->line($token->plainTextToken);
        
        return 0;
    }
}
