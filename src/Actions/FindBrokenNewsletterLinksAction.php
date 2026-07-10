<?php

namespace JeffersonGoncalves\Newsletter\Actions;

use DOMDocument;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Newsletter\Models\Newsletter;

class FindBrokenNewsletterLinksAction
{
    /**
     * @return Collection<int, array{url: string, status: int|null, error: string|null}>
     */
    public function handle(Newsletter $newsletter): Collection
    {
        $broken = [];

        foreach ($this->extractUrls($newsletter->content) as $url) {
            $result = $this->check($url);

            if ($this->isBroken($result)) {
                $broken[] = $result;
            }
        }

        return collect($broken);
    }

    /**
     * @param  array{url: string, status: int|null, error: string|null}  $result
     */
    protected function isBroken(array $result): bool
    {
        return $result['status'] === null || $result['status'] >= 400;
    }

    /**
     * @return Collection<int, string>
     */
    protected function extractUrls(string $html): Collection
    {
        $document = new DOMDocument;

        $internalErrors = libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="utf-8" ?><div>'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);

        $urls = collect();

        foreach ($document->getElementsByTagName('a') as $anchor) {
            $urls->push($anchor->getAttribute('href'));
        }

        foreach ($document->getElementsByTagName('img') as $image) {
            $urls->push($image->getAttribute('src'));
        }

        return $urls
            ->filter(fn (string $url): bool => (bool) preg_match('/^https?:\/\//i', $url))
            ->unique()
            ->values();
    }

    /**
     * @return array{url: string, status: int|null, error: string|null}
     */
    protected function check(string $url): array
    {
        try {
            $response = Http::head($url);

            if ($response->status() === 405) {
                $response = Http::get($url);
            }

            return ['url' => $url, 'status' => $response->status(), 'error' => null];
        } catch (ConnectionException $exception) {
            return ['url' => $url, 'status' => null, 'error' => $exception->getMessage()];
        }
    }
}
