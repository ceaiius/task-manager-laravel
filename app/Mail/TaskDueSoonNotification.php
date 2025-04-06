<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskDueSoonNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Task Due Soon: ' . $this->task->title,
        );
    }


    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tasks.due_soon',
             with: [
                 'taskTitle' => $this->task->title,
                 'dueDateFormatted' => $this->task->due_date->format('M d, Y'),
                 'userName' => $this->task->user->name,
             ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
