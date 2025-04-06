<x-mail::message>
# Task Reminder

Hello {{ $userName }},

Just a friendly reminder that your task is due soon:

**Task:** {{ $taskTitle }}
**Due Date:** {{ $dueDateFormatted }}

Please ensure it's completed on time.

{{-- Optional Button --}}
<x-mail::button :url="url('/dashboard')"> {{-- Or a more specific task URL if available --}}
View Task Board
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
