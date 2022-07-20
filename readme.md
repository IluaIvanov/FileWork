## FileWork

A class that allows you to save files in a project, while checking the file format, as well as output errors in case of problems with saving

### Example call

```php

$file = new fileWork($image, '/test', DEFAULT_FORMAT_ERROR, 'png|jpg|jpeg');
$img = $file->getFileName();

```