<?php

namespace JeffersonGoncalves\Newsletter\Enums;

enum RecipientDeliveryStatus: string
{
    case Sent = 'sent';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Sent => __('newsletter::statuses.recipient_sent'),
            self::Failed => __('newsletter::statuses.recipient_failed'),
        };
    }
}
