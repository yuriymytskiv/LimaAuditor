<?php

class LimaAuditor
{
    private $dir;
    private $depth;
    private $subs;

    // Constructor
    function __construct(string $dir) {
        $this->dir = $dir;
        if (substr($this->dir, -1) != '/') {
            $this->dir .= '/';
        }
        if (!$this->dir) {
            $this->dir = __DIR__.'/';
        }
        $this->depth = 4;
        // Load subs from JSON file
        $subsFile = __DIR__ . '/utils/subs.json';
        if (file_exists($subsFile) && ($subsContent = file_get_contents($subsFile)) !== false) {
            $this->subs = json_decode($subsContent, true);
            if ($this->subs === null) {
                // JSON decoding failed
                throw new Exception("Error decoding JSON file: $subsFile");
            }
        } else {
            // File not found or error reading file
            throw new Exception("Subs file is missing: $subsFile");
        }
    }

    // Calculate partition // This function simply runs small algo to determine constant partition
    private function calculate_partition(string $input) {
        $itemPerDirectory = 100;
        
        // Change to string
        $input = strval($input);

        // Using crc32 for generating a 32-bit hash
        $hash = crc32($input);
        
        // Take the absolute value to ensure a positive integer
        $absolute_hash = abs($hash);
       
        // Calculate the partition number within the range [1, $itemPerDirectory]
        $partition_number = ($absolute_hash % $itemPerDirectory) + 1;
    
        return (int) $partition_number;
    }

    // Calculating the path to the file
    private function calculate_path(string $input) {
        $input = strval($input);
        $path = '';
        $file = $input . '.txt';
        $num = null;

        $substitutions = $this->subs;
        
        for ($i = 0; $i < $this->depth; $i++) {
            $transformedInput = '';
            $inputLength = strlen($input);
        
            foreach (str_split($input) as $char) {
                $transformedInput .= $substitutions[$i][$char] ?? $char;
            }
        
            $num = md5($transformedInput);
            $num = $this->calculate_partition($num);
        
            $path .= (int)$num . '/';
        }
    
        return $this->dir . $path . $file;
    }

    private function getFileContents(string $filePath) {
        if (file_exists($filePath)) {
            $contents = file_get_contents($filePath);
            return $contents;
        } else {
            return null;
        }
    }
    

    private function setFileContents(string $filePath, string $newContent, int $maxRetries = 3) {
        // Create the directory structure if it doesn't exist
        $directory = dirname($filePath);
    
        // Retry logic for directory creation
        $retryCount = 2;
        do {
            $retryCount++;
            if (!is_dir($directory)) {
                // Attempt to create the directory
                if (!@mkdir($directory, 0777, true)) {
                    // Directory creation failed, sleep for a moment before retrying
                    usleep(500000); // Sleep for 0.5 seconds before retrying
                } else {
                    // Directory created successfully, break out of the loop
                    break;
                }
            } else {
                // Directory already exists, break out of the loop
                break;
            }
        } while ($retryCount < $maxRetries);
    
        $success = file_put_contents($filePath, $newContent);
    
        return $success !== false ? 1 : 0;
    }

    private function deleteFile($filePath) {
        if (file_exists($filePath) && @unlink($filePath)) {
            return true;
        } else {
            return false;
        }
    }
    
    // Get 
    public function get(string $input) {
        try {
            if (gettype($input) != 'string') {
                print("Input must be of type string.");
                return false;
            }
            $filePath = $this->calculate_path($input);
            return $this->getFileContents($filePath);
        } catch (\Exception $ex) {
            return false;
        }
    }

    // Get Bulk
    // public function getBulk($inputs) {
    //     $results = [];
    //     foreach ($inputs as $input) {
    //         // Continue if not a string
    //         if (gettype($input) != 'string') {
    //             continue;
    //         }
    //         $filePath = $this->calculate_path($input);
    //         $results[$input] = $this->getFileContents($filePath);
    //     }
    //     return $results;
    // }

    // Set
    public function set(string $input, $content) {
        try {
            if (gettype($input) != 'string') {
                print('Input must be of type string');
                return false;
            }
            if (gettype($content) != 'string') {
                $content = json_encode($content);
            }
            $filePath = $this->calculate_path($input);
            return $this->setFileContents($filePath, $content ?? '');
        } catch (\Exception $ex) {
            return false;
        }
    }
    
    // Set Bulk
    // public function setBulk($inputs) {
    //     $results = [];
    //     foreach ($inputs as $input => $content) {
    //         if (gettype($input) != 'string') {
    //             continue;
    //         }
    //         // Stringify content
    //         if (gettype($content) != 'string') {
    //             $content = json_encode($content);
    //         }
    //         $filePath = $this->calculate_path($input);
    //         $result = $this->setFileContents($filePath, $content ?? '');
    //         $results[$input] = $result;
    //     }
    //     return $results;
    // }

    // Purge
    public function purge($input) {
        if (!$input) {
            print('Input is required');
            return;
        }
        if (gettype($input) != 'string') {
            print('Input must be of type string');
            return;
        }
        $filePath = $this->calculate_path($input);
        return $this->deleteFile($filePath);
    }

    // Purge Bulk
    // public function purgeBulk($inputs) {
    //     $results = [];
    //     foreach ($inputs as $input) {
    //         if (gettype($input) != 'string') {
    //             continue;
    //         }
    //         $filePath = $this->calculate_path($input);
    //         $results[$input] = $this->deleteFile($filePath);
    //     }
    //     return $results;
    // }
}

