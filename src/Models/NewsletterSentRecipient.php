<?php

namespace JeffersonGoncalves\Newsletter\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Newsletter\Database\Factories\NewsletterSentRecipientFactory;
use JeffersonGoncalves\Newsletter\Enums\RecipientDeliveryStatus;

/**
 * @property int $id
 * @property int $newsletter_id
 * @property string $email
 * @property RecipientDeliveryStatus $status
 * @property string|null $error_message
 * @property Carbon|null $sent_at
 */
class NewsletterSentRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'newsletter_id',
        'email',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'status' => RecipientDeliveryStatus::class,
        'sent_at' => 'datetime',
    ];

    public function newsletter(): BelongsTo
    {
        return $this->belongsTo(Newsletter::class);
    }

    protected static function newFactory(): NewsletterSentRecipientFactory
    {
        return NewsletterSentRecipientFactory::new();
    }
}
