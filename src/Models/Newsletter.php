<?php

namespace JeffersonGoncalves\Newsletter\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Newsletter\Database\Factories\NewsletterFactory;
use JeffersonGoncalves\Newsletter\Enums\NewsletterContentType;
use JeffersonGoncalves\Newsletter\Enums\NewsletterStatus;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property int $id
 * @property string $subject
 * @property string|null $sender_name
 * @property string $sender_email
 * @property string $content
 * @property NewsletterContentType $content_type
 * @property NewsletterStatus $status
 * @property string|null $route
 * @property bool $published
 * @property bool $send_unsubscribe_link
 * @property bool $send_webview_link
 * @property Carbon|null $scheduled_at
 * @property Carbon|null $sent_at
 * @property int $total_recipients
 * @property int $total_views
 * @property string|null $utm_campaign
 */
class Newsletter extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'subject',
        'sender_name',
        'sender_email',
        'content',
        'content_type',
        'status',
        'route',
        'published',
        'send_unsubscribe_link',
        'send_webview_link',
        'scheduled_at',
        'sent_at',
        'total_recipients',
        'total_views',
        'utm_campaign',
    ];

    protected $casts = [
        'content_type' => NewsletterContentType::class,
        'status' => NewsletterStatus::class,
        'published' => 'boolean',
        'send_unsubscribe_link' => 'boolean',
        'send_webview_link' => 'boolean',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'total_recipients' => 'integer',
        'total_views' => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')->useDisk('public');
    }

    public function emailGroups(): BelongsToMany
    {
        return $this->belongsToMany(EmailGroup::class, 'newsletter_email_group');
    }

    public function sentRecipients(): HasMany
    {
        return $this->hasMany(NewsletterSentRecipient::class);
    }

    protected function isFullySent(): Attribute
    {
        return Attribute::get(fn (): bool => $this->status === NewsletterStatus::Sent);
    }

    protected static function newFactory(): NewsletterFactory
    {
        return NewsletterFactory::new();
    }
}
