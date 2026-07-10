<?php

declare(strict_types=1);

namespace Hirasso\Prose;

/**
 * Configuration for {@see Prose::format()}.
 *
 * All options are optional; the defaults produce sensible output for
 * rich text / prose content.
 */
final readonly class ProseOptions
{
    /**
     * @param list<string>|null $allowedTags Tags to keep when stripping (e.g. ['p', 'a', 'strong']). null = don't strip.
     * @param bool $obfuscate Obfuscate email/phone links against spam bots.
     * @param list<string> $removeEmptyElements Selectors of whitespace-only elements to prune.
     * @param string|null $siteUrl The site's own URL. Required to detect & mark external links; null = skip.
     * @param array<string, string> $externalLinkAttributes Attributes added to external links.
     */
    public function __construct(
        public ?array $allowedTags = null,
        public bool $obfuscate = true,
        public array $removeEmptyElements = [],
        public ?string $siteUrl = null,
        public array $externalLinkAttributes = ['data-external' => '', 'target' => '_blank'],
    ) {
    }
}
