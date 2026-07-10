<?php

namespace JeffersonGoncalves\Newsletter\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Newsletter\Database\Factories\EmailGroupMemberFactory;

/**
 * @property int $id
 * @property int $email_group_id
 * @property string $email
 * @property bool $unsubscribed
 * @property Carbon|null $unsubscribed_at
 * @property Carbon|null $confirmed_at
 */
class EmailGroupMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_group_id',
        'email',
        'unsubscribed',
        'unsubscribed_at',
        'confirmed_at',
    ];

    protected $casts = [
        'unsubscribed' => 'boolean',
        'unsubscribed_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    public function emailGroup(): BelongsTo
    {
        return $this->belongsTo(EmailGroup::class);
    }

    protected static function newFactory(): EmailGroupMemberFactory
    {
        return EmailGroupMemberFactory::new();
    }
}
