<?php

declare(strict_types=1);

use Hirasso\Prose\Prose;
use Hirasso\Prose\ProseOptions;

it('returns empty input untouched', function () {
    expect(Prose::format(''))->toBe('');
    expect(Prose::format('   '))->toBe('   ');
});

it('autolinks raw urls', function () {
    $result = Prose::format('<p>Visit https://example.com today</p>', new ProseOptions(
        obfuscate: false,
    ));

    expect($result)->toContain('<a')->toContain('href="https://example.com"');
});

it('marks external links', function () {
    $result = Prose::format(
        '<p><a href="https://external.com">out</a> <a href="/internal">in</a></p>',
        new ProseOptions(obfuscate: false, siteUrl: 'https://example.com'),
    );

    expect($result)
        ->toContain('data-external')
        ->toContain('target="_blank"');
});

it('does not mark internal links as external', function () {
    $result = Prose::format(
        '<p><a href="/internal">in</a></p>',
        new ProseOptions(obfuscate: false, siteUrl: 'https://example.com'),
    );

    expect($result)->not->toContain('data-external');
});

it('strips tags to the allowlist', function () {
    $result = Prose::format(
        '<p>keep</p><script>alert(1)</script>',
        new ProseOptions(obfuscate: false, allowedTags: ['p']),
    );

    expect($result)->toContain('<p>keep</p>')->not->toContain('<script>');
});

it('removes empty paragraphs', function () {
    $result = Prose::format(
        '<p>kept</p><p>&nbsp;</p><p>   </p>',
        new ProseOptions(obfuscate: false),
    );

    expect(substr_count($result, '<p'))->toBe(1);
});

it('does not wrap output in a body tag', function () {
    $result = Prose::format('<p>hi</p>', new ProseOptions(obfuscate: false));

    expect($result)->not->toContain('<body');
});

it('throws on a full html document', function () {
    Prose::format('<html><body><p>hi</p></body></html>');
})->throws(InvalidArgumentException::class);

it('throws on a body wrapper', function () {
    Prose::format('<body>foo</body>');
})->throws(InvalidArgumentException::class);

it('throws on a doctype', function () {
    Prose::format('<!doctype html><p>hi</p>');
})->throws(InvalidArgumentException::class);

it('throws on a head tag', function () {
    Prose::format('<head><title>x</title></head><p>hi</p>');
})->throws(InvalidArgumentException::class);

it('accepts a bare prose fragment', function () {
    $result = Prose::format('<p>hi</p>', new ProseOptions(obfuscate: false));

    expect($result)->toBe('<p>hi</p>');
});
