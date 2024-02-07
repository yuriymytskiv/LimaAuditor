# LimaAuditor

LimaAuditor is a unix file helper. It partitions files and stores json/text as the content. It is very fast as it uses 
partitioning concepts along side the speed of the unix xfs filesystem.

## Context

This file system was created for a specific use case where my team required to store extra text data. We did not want to store this data in the database, so I brainstormed this idea. 

## Rules

In order to avoid hot partitions, it is critical to use unique IDs. Although my abstraction of the ID does its job to further abstract the ID, it is still important, especially when storing large amounts of data, to ensure each key is unique.

Currently, I'm still debating if grouping the files into specific partitions is a smart idea or if the current approach will suffice.

The system is currently set to store 100B files or whatever your system can handle. If you are interested in the depth of storage, look through the code. You will be able to see how the directories are made. You are welcome to change the depth for your specific use case.

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

Me - Yuriy Mytskiv 

Pull requests are welcome.