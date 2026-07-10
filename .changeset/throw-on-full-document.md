---
"hirasso/prose": minor
---

`Prose::format()` now throws an `InvalidArgumentException` when passed a full HTML document (`<!doctype>`, `<html>`, `<head>` or `<body>`) instead of trying to preserve the wrapper. Prose only formats prose fragments.
