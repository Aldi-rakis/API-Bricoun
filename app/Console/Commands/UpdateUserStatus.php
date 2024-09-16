<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User; // pastikan model User terhubung
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateUserStatus extends Command
{
    // Nama command
    protected $signature = 'user:update-status';
    
    // Deskripsi command
    protected $description = 'Update user status every detik';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Log::info('User status update command is running.');
    
        User::where('status', 'sudah menjawab')
            ->update(['status' => 'belum menjawab', 'updated_at' => now()]);
    
        $this->info('User statuses have been updated.');
        Log::info('User statuses have been updated successfully.');
    }
}
