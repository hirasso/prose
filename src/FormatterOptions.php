<?php

declare(strict_types=1);

namespace Hirasso\Prose;

use Asika\Autolink\AutolinkOptions;

/**
 * Configuration for {@see Formatter::format()}.
 *
 * All options are optional; the defaults produce sensible output for
 * rich text / prose content.
 */
final readonly class FormatterOptions
{
    /**
     * @param list<string>|null $allowedTags Tags to keep when stripping (e.g. ['p', 'a', 'strong']). null = don't strip.
     * @param AutolinkOptions|null $autolink Autolink options. null = library defaults ({@see self::autolinkOptions()}).
     * @param bool $obfuscate Obfuscate email/phone links against spam bots.
     * @param list<string> $removeEmptyElements Selectors of whitespace-only elements to prune.
     * @param string|null $siteUrl The site's own URL. Required to detect & mark external links; null = skip.
     * @param array<string, string> $externalLinkAttributes Attributes added to external links.
     */
    public function __construct(
        public ?array $allowedTags = null,
        public ?AutolinkOptions $autolink = null,
        public bool $obfuscate = true,
        public array $removeEmptyElements = ['p'],
        public ?string $siteUrl = null,
        public array $externalLinkAttributes = ['data-external' => '', 'target' => '_blank'],
    ) {
    }

    /**
     * The autolink options to use, falling back to library defaults.
     */
    public function autolinkOptions(): AutolinkOptions
    {
        return $this->autolink ?? new AutolinkOptions(
            stripScheme: true,
            textLimit: 35,
            autoTitle: false,
            escape: true,
            // linkNoScheme poses issues with e.g. "Architekt.innen"
            linkNoScheme: false,
        );
    }
}
