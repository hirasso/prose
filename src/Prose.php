<?php

declare(strict_types=1);

namespace Hirasso\Prose;

use Asika\Autolink\Autolink;
use Dom\HTMLDocument;
use InvalidArgumentException;

use function Hirasso\HTMLObfuscator\obfuscate;

/**
 * Formats rich text / prose HTML (e.g. WYSIWYG field values).
 *
 * Framework-agnostic: pass a string, get a string back. Wire it into your
 * CMS / framework yourself (e.g. a WordPress `acf/format_value` filter).
 */
final class Prose
{
    /**
     * Format a prose HTML string.
     */
    public static function format(string $html, ?ProseOptions $options = null): string
    {
        if (trim($html) === '') {
            return $html;
        }

        self::assertIsFragment($html);

        $options ??= new ProseOptions();

        if ($options->allowedTags !== null) {
            $html = strip_tags($html, $options->allowedTags);
        }

        $autolink = new Autolink($options->autolinkOptions());
        $html = $autolink->convert($html);
        $html = $autolink->convertEmail($html);

        $doc = HTMLDocument::createFromString($html, LIBXML_NOERROR);

        if ($options->siteUrl !== null) {
            self::markExternalLinks($doc, $options);
        }

        if ($options->obfuscate) {
            obfuscate($doc)->saveDocument();
        }

        self::removeEmptyElements($doc, $options->removeEmptyElements);

        return $doc->body->innerHTML ?? '';
    }

    /**
     * Guard against full HTML documents: Prose only formats prose fragments.
     */
    private static function assertIsFragment(string $html): void
    {
        if (preg_match('/<!doctype[\s>]|<(?:html|head|body)[\s>]/i', $html) === 1) {
            throw new InvalidArgumentException(
                'Prose::format() expects a prose fragment, not a full HTML document '
                . '(<!doctype>, <html>, <head> or <body> found).'
            );
        }
    }

    /**
     * Add the configured attributes to every external link.
     */
    private static function markExternalLinks(HTMLDocument $doc, ProseOptions $options): void
    {
        $siteUrl = (string) $options->siteUrl;

        foreach ($doc->querySelectorAll('a[href]') as $el) {
            if (!self::isExternalUrl($el->getAttribute('href') ?? '', $siteUrl)) {
                continue;
            }
            foreach ($options->externalLinkAttributes as $name => $value) {
                $el->setAttribute($name, $value);
            }
        }
    }

    /**
     * Is the given URL external relative to $siteUrl?
     *
     * URLs without a host (relative paths, fragments, mailto:, tel:, …) are
     * treated as internal.
     */
    private static function isExternalUrl(string $url, string $siteUrl): bool
    {
        if ($url === '') {
            return false;
        }

        $host = parse_url($url, PHP_URL_HOST);

        if ($host === null || $host === false) {
            return false;
        }

        $siteHost = parse_url($siteUrl, PHP_URL_HOST);

        return strcasecmp($host, is_string($siteHost) ? $siteHost : '') !== 0;
    }

    /**
     * Remove whitespace-only elements matching $selector.
     * @param list<string> $selectors
     */
    private static function removeEmptyElements(HTMLDocument $doc, array $selectors): void
    {
        $toRemove = [];
        $selectorString = implode(',', $selectors);

        foreach ($doc->querySelectorAll($selectorString) as $node) {
            if (self::isWhitespaceOnly((string) $node->textContent)) {
                $toRemove[] = $node;
            }
        }

        foreach ($toRemove as $node) {
            $node->remove();
        }
    }

    /**
     * Is the text only whitespace, including zero-width and non-breaking chars?
     */
    private static function isWhitespaceOnly(string $text): bool
    {
        $stripped = preg_replace(
            '/[\s\p{Z}\x{200B}\x{200C}\x{200D}\x{FEFF}\x{2060}\x{00AD}]+/u',
            '',
            $text
        );

        return $stripped === '';
    }
}
