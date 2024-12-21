<?php

namespace App\Notifications;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewBlogNotification extends Notification
{
    use Queueable;

    protected $blog;
    protected $author;

    /**
     * Create a new notification instance.
     */
    public function __construct(Blog $blog, User $author)
    {
        $this->blog = $blog;
        $this->author = $author;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('A new post has been published')
            ->greeting('Hello!')
            ->line('A new post has been published by ' . $this->author->name)
            ->line($this->author->email)
            ->line('Title: ' . $this->blog->title)
            ->action('View Post', url('/api/blogs/' . $this->blog->id))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */

        public function toDatabase($notifiable)
    {
        return [
            'title' => $this->blog->title,
            'author_name' => $this->author->name,
            'author_email' => $this->author->email,
            'url' => url('/api/blogs/' . $this->blog->id),
        ];
    }
}
