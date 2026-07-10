# hirasso/prose

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hirasso/prose.svg)](https://packagist.org/packages/hirasso/prose)
[![Test Status](https://img.shields.io/github/actions/workflow/status/hirasso/prose/ci.yml?label=tests)](https://github.com/hirasso/prose/actions/workflows/ci.yml)
[![Code Coverage](https://img.shields.io/codecov/c/github/hirasso/prose?label=coverage%20%28whatever%20that%20entails%29)](https://app.codecov.io/gh/hirasso/prose)

**Format rich text / prose HTML in PHP. Fully HTML5-compatible 🐘**

A tiny, framework-agnostic formatter for WYSIWYG / rich text content. Pass a
string, get a string back:

- Autolink raw URLs and email addresses
- Obfuscate email/phone links against spam bots (via [`hirasso/html-obfuscator`](https://github.com/hirasso/html-obfuscator))
- Mark external links (`target="_blank"`, custom attributes)
- Strip tags to an allowlist
- Remove whitespace-only elements (e.g. empty `<p>`)

## Installation

```shell
composer require hirasso/prose
```

## Usage

```php
use Hirasso\Prose\Prose;
use Hirasso\Prose\ProseOptions;

// Minimal: sensible defaults (autolink + obfuscate + prune empty <p>)
echo Prose::format($html);

// Configured
echo Prose::format($html, new ProseOptions(
    allowedTags: ['p', 'a', 'br', 'strong', 'em', 'ul', 'li'],
    siteUrl: 'https://example.com',      // required to detect external links
    removeEmptyElements: ['p'],
    obfuscate: true,
));
```

> [!NOTE]
> `Prose::format()` expects a **prose fragment**, not a full HTML document. It
> throws an `InvalidArgumentException` if the input contains `<!doctype>`,
> `<html>`, `<head>` or `<body>`.

### Options

| Option | Default | Purpose |
|--------|---------|---------|
| `allowedTags` | `null` | Tags to keep when stripping. `null` = don't strip. |
| `autolink` | library defaults | `Asika\Autolink\AutolinkOptions` for URL/email linking. |
| `obfuscate` | `true` | Obfuscate email/phone links. |
| `removeEmptyElements` | `['p']` | Selectors of whitespace-only elements to prune. |
| `siteUrl` | `null` | Your site's URL. Required to detect external links. |
| `externalLinkAttributes` | `['data-external' => '', 'target' => '_blank']` | Attributes added to external links. |

## WordPress / ACF

The package ships no framework glue. Wire it into an `acf/format_value` filter
in your project:

```php
add_filter('acf/format_value/type=wysiwyg', function (mixed $value, $postID, array $field): mixed {
    if (!is_string($value) || trim($value) === '') {
        return $value;
    }
    return Prose::format($value, new ProseOptions(
        allowedTags: ['p', 'a', 'br', 'blockquote', 'strong', 'b', 'i', 'em', 'ul', 'li', 'sup'],
        siteUrl: home_url(),
    ));
}, 11, 3);

// Inject the obfuscator client script once, in <head>:
add_action('wp_head', fn () => print(\Hirasso\HTMLObfuscator\clientScript()));
```

## License

MIT © [Rasso Hilber](https://rassohilber.com)
