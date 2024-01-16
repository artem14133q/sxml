# SXML

A simple php library for manage xml/html documents.

## Php version
`^8.1`

## Installation
`composer require artem14133q/sxml`

## Usage
```php
use Sxml\Documents\HtmlDocument;

$html = "
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <div>Hello World!</div>
</body>
</html>
";

$doc = HtmlDocument($html);

$body = $doc->getHtmlNode()->findByName("body");
$div = $body->findByName("div");

$div->setValue("Hello SXML!");

$doc->writer->asText();
```

Input:
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <div>Hello SXML!</div>
</body>
</html>
```

## License
`MIT`