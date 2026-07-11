# php-prose

## 0.2.0

### Minor Changes

- b505894: Remove autolinking of raw URLs & emails. Prose no longer depends on `asika/autolink`, and the `autolink` option on `ProseOptions` is gone.
- 2096d12: Change the default of the `removeEmptyElements` option from `['p']` to `[]`. Empty elements are now kept unless explicitly configured.

### Patch Changes

- 7027458: External link `class` attributes are now appended to any existing classes on the link instead of overwriting them. All other `externalLinkAttributes` still overwrite as before.

## 0.1.0

### Minor Changes

- 5235fc1: `Prose::format()` now throws an `InvalidArgumentException` when passed a full HTML document (`<!doctype>`, `<html>`, `<head>` or `<body>`) instead of trying to preserve the wrapper. Prose only formats prose fragments.
