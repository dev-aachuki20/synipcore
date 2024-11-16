<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AdminMessage;
use Carbon\Carbon;
class UpdateAdminMessageStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update admin message status from 1 to 0 if older than 30 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        $updatedMessages = AdminMessage::where('status', 1)
            ->where('created_at', '<', $thirtyDaysAgo)
            ->update(['status' => 0]);

        // Output how many messages were updated
        $this->info("$updatedMessages messages status updated from 1 to 0.");
    }
}
