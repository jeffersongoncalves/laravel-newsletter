<?php

namespace JeffersonGoncalves\Newsletter\Enums;

enum NewsletterContentType: string
{
    case RichText = 'rich_text';
    case Markdown = 'markdown';
    case Html = 'html';

    public function label(): string
    {
        return match ($this) {
            self::RichText => __('newsletter::content_types.rich_text'),
            self::Markdown => __('newsletter::content_types.markdown'),
            self::Html => __('newsletter::content_types.html'),
        };
    }
}
