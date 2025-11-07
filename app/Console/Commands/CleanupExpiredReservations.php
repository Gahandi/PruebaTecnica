<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TicketReservation;

class CleanupExpiredReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired ticket reservations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredCount = TicketReservation::where('reserved_until', '<', now())->count();
        
        if ($expiredCount > 0) {
            TicketReservation::where('reserved_until', '<', now())->delete();
            $this->info("Cleaned up {$expiredCount} expired reservations.");
        } else {
            $this->info('No expired reservations found.');
        }
        
        return 0;
    }
}
