<?php

namespace JeffersonGoncalves\Newsletter\Actions;

use DOMDocument;
use DOMElement;
use JeffersonGoncalves\Newsletter\Models\Newsletter;

class InjectNewsletterTrackingAction
{
    public function handle(string $html, Newsletter $newsletter): string
    {
        if (trim($html) === '' || ! config('newsletter.tracking_enabled', true)) {
            return $html;
        }

        $html = $this->appendUtmParametersToLinks($html, $newsletter);

        if ($newsletter->send_webview_link) {
            $html .= $this->trackingPixel($newsletter);
        }

        return $html;
    }

    protected function appendUtmParametersToLinks(string $html, Newsletter $newsletter): string
    {
        $document = new DOMDocument;

        $internalErrors = libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="utf-8" ?><div>'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);

        $wrapper = $document->getElementsByTagName('div')->item(0);

        if (! $wrapper instanceof DOMElement) {
            return $html;
        }

        foreach ($document->getElementsByTagName('a') as $anchor) {
            $href = $anchor->getAttribute('href');

            if (! preg_match('/^https?:\/\//i', $href)) {
                continue;
            }

            $anchor->setAttribute('href', $this->appendUtmParameters($href, $newsletter));
        }

        $trackedHtml = '';

        foreach ($wrapper->childNodes as $child) {
            $trackedHtml .= $document->saveHTML($child);
        }

        return $trackedHtml;
    }

    protected function appendUtmParameters(string $url, Newsletter $newsletter): string
    {
        $parts = parse_url($url);

        if ($parts === false) {
            return $url;
        }

        parse_str($parts['query'] ?? '', $existingQuery);

        $utmParameters = array_filter([
            'utm_source' => 'newsletter',
            'utm_medium' => 'email',
            'utm_campaign' => $newsletter->utm_campaign,
        ]);

        $parts['query'] = http_build_query(array_merge($existingQuery, $utmParameters));

        return $this->buildUrl($parts);
    }

    /**
     * @param  array<string, int|string>  $parts
     */
    protected function buildUrl(array $parts): string
    {
        $scheme = isset($parts['scheme']) ? $parts['scheme'].'://' : '';
        $host = $parts['host'] ?? '';
        $port = isset($parts['port']) ? ':'.$parts['port'] : '';
        $path = $parts['path'] ?? '';
        $query = isset($parts['query']) && $parts['query'] !== '' ? '?'.$parts['query'] : '';
        $fragment = isset($parts['fragment']) ? '#'.$parts['fragment'] : '';

        return $scheme.$host.$port.$path.$query.$fragment;
    }

    protected function trackingPixel(Newsletter $newsletter): string
    {
        $url = route('newsletter.track.open', $newsletter);

        return '<img src="'.$url.'" width="1" height="1" alt="" style="display:none;border:0;">';
    }
}
