<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class BulkCreateUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $users;

    /**
     * Create a new job instance.
     */
    public function __construct($users)
    {
        $this->users = $users;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $createdUsers = [];
        $failedUsers = [];

        foreach ($this->users as $userData) {
            try {
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password']),
                    'role' => $userData['role'],
                ]);

                $createdUsers[] = $user;
                Log::info("User created successfully: {$user->email}");
            } catch (\Exception $e) {
                $failedUsers[] = [
                    'data' => $userData,
                    'error' => $e->getMessage()
                ];
                Log::error("Failed to create user: {$userData['email']}", [
                    'error' => $e->getMessage(),
                    'data' => $userData
                ]);
            }
        }

        Log::info("Bulk user creation completed", [
            'total_requested' => count($this->users),
            'created' => count($createdUsers),
            'failed' => count($failedUsers)
        ]);
    }
}
