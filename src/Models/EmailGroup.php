<?php

namespace JeffersonGoncalves\Newsletter\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use JeffersonGoncalves\Newsletter\Database\Factories\EmailGroupFactory;

/**
 * @property int $id
 * @property string $title
 */
class EmailGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(EmailGroupMember::class);
    }

    public function newsletters(): BelongsToMany
    {
        return $this->belongsToMany(Newsletter::class, 'newsletter_email_group');
    }

    /**
     * Count of confirmed, non-unsubscribed members. Intentionally not persisted
     * as a column so it always reflects the current state of the members table.
     */
    protected function subscribersCount(): Attribute
    {
        return Attribute::get(
            fn (): int => $this->members()
                ->where('unsubscribed', false)
                ->whereNotNull('confirmed_at')
                ->count()
        );
    }

    protected static function newFactory(): EmailGroupFactory
    {
        return EmailGroupFactory::new();
    }
}
