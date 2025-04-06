<?php

namespace App\Console\Commands;

use App\Mail\TaskDueSoonNotification;
use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTaskDueSoonEmails extends Command
{

    protected $signature = 'app:send-task-due-soon-emails';


    protected $description = 'Find tasks due tomorrow and send email notifications to users.';

    public function handle()
    {
        $this->info('Checking for tasks due tomorrow...');
        Log::info('Running SendTaskDueSoonEmails command...');

        $tomorrow = Carbon::tomorrow()->toDateString();


        $tasksDueSoon = Task::with('user')
            ->where('status', '!=', 'completed')
            ->whereDate('due_date', $tomorrow)
            ->get();

        if ($tasksDueSoon->isEmpty()) {
            $this->info('No tasks due tomorrow found.');
            Log::info('No tasks due tomorrow found.');
            return 0;
        }

        $this->info("Found {$tasksDueSoon->count()} tasks due tomorrow. Sending notifications...");
        Log::info("Found {$tasksDueSoon->count()} tasks due tomorrow. Sending notifications...");

        $sentCount = 0;
        foreach ($tasksDueSoon as $task) {
            if ($task->user) {
                try {

                    Mail::to($task->user)->send(new TaskDueSoonNotification($task));
                    $this->line(" - Notification sent to {$task->user->email} for task ID: {$task->id}");
                    Log::info("Notification queued/sent for Task ID: {$task->id} to User ID: {$task->user->id}");
                    $sentCount++;
                } catch (\Exception $e) {
                    $this->error("   Failed to send notification for task ID: {$task->id}. Error: {$e->getMessage()}");
                    Log::error("Failed sending TaskDueSoonNotification for Task ID: {$task->id}. Error: {$e->getMessage()}");
                }
            } else {
                 $this->warn("   Skipping task ID: {$task->id} - User relationship not loaded or user missing.");
                 Log::warning("Skipping TaskDueSoonNotification for Task ID: {$task->id} - User missing.");
            }
        }

        $this->info("Finished sending notifications. Total sent: {$sentCount}");
        Log::info("Finished SendTaskDueSoonEmails command. Sent: {$sentCount}");
        return 0;
    }
}
