# LimaAuditor

LimaAuditor is a usa unix file helper. It partitions files and stores json/text as the content.

## Installation

Drop the function file anywhere you want in your project & include it as needed.

## Usage
```
require 'src/LimaAuditor.php';

define('STORAGE_ROOT', '/path/to/directory');
$LimaAuditor = new LimaAuditor(STORAGE_ROOT);

Store
$storeResult = $LimaAuditor->set($randomId, $content);

Read
$fileContent = $LimaAuditor->get($randomId);

or get path and then get the contents in another way

$filePath = $LimaAuditor->getFilePath($randomId);

```
## Contributing

Pull requests are welcome.