<?php

namespace JeffersonGoncalves\Newsletter\Enums;

enum NewsletterStatus: string
{
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Sending = 'sending';
    case Sent = 'sent';

    public function label(): string
    {
        return match ($this) {
            self::Draft => __('newsletter::statuses.draft'),
            self::Scheduled => __('newsletter::statuses.scheduled'),
            self::Sending => __('newsletter::statuses.sending'),
            self::Sent => __('newsletter::statuses.sent'),
        };
    }
}
