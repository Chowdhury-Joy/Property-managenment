<?php

namespace App\Console\Commands;

use App\Models\Lease;
use App\Models\RentPayment;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateRentInvoices extends Command
{
    protected $signature = 'rent:generate';
    protected $description = 'Generate monthly rent payment records for all active leases';

    public function handle()
    {
        $this->info('Starting rent invoice generation...');
        $generated = 0;

        $activeLeases = Lease::where('status', 'active')->get();

        foreach ($activeLeases as $lease) {
            // Calculate the due date for the current month
            $dueDate = Carbon::now()->day($lease->due_day);
            
            // If today is past the due date, we still generate it for this month
            $month = $dueDate->month;
            $year = $dueDate->year;

            // Check if an invoice already exists for this month/year
            $exists = RentPayment::where('lease_id', $lease->id)
                ->whereMonth('due_date', $month)
                ->whereYear('due_date', $year)
                ->exists();

            if (!$exists) {
                // Determine status: if today is past the due date, it's late. Otherwise, upcoming.
                $status = Carbon::now()->greaterThan($dueDate) ? 'late' : 'upcoming';

                RentPayment::create([
                    'lease_id' => $lease->id,
                    'amount' => $lease->rent_amount,
                    'due_date' => $dueDate,
                    'status' => $status,
                ]);
                $generated++;
            }
        }

        $this->info("Successfully generated {$generated} new rent invoices.");
        return 0;
    }
}
