<?php

class Utils
{
    /**
     * Reads a JSON file and decodes it into an array.
     * 
     * @param string $relativePath The relative path to the JSON file.
     * @return array|null Decoded JSON data or null if an error occurs.
     */
    public static function readJson(string $relativePath): ?array
    {
        // Construct the full path to the JSON file
        $fullPath = __DIR__ . '/../' . $relativePath;

        // Check if the file exists
        if (!file_exists($fullPath)) {
            error_log("File not found: $fullPath");
            return null;
        }

        // Read and decode the JSON file
        $json = file_get_contents($fullPath);
        return json_decode($json, true);
    }
}