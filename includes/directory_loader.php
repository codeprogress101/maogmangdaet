<?php
/**
 * Utility helpers for loading the municipal directory from a JSON file.
 */

if (!function_exists('load_directory_entries')) {
    /**
     * Load directory entries from a JSON source file.
     *
     * @param string $filePath Absolute path to the JSON file.
     * @param string|null $error Populated with a human-readable error on failure.
     * @return array<int, array<string, string>>
     */
    function load_directory_entries(string $filePath, ?string &$error = null): array
    {
        $error = null;

        if (!is_readable($filePath)) {
            $error = 'Directory data file is missing or unreadable.';
            return [];
        }

        $json = file_get_contents($filePath);
        if ($json === false) {
            $error = 'Unable to read the directory data file.';
            return [];
        }

        $data = json_decode($json, true);
        if (!is_array($data)) {
            $error = 'Directory data file contains invalid JSON.';
            return [];
        }

        if (array_key_exists('entries', $data) && is_array($data['entries'])) {
            $entries = $data['entries'];
        } elseif (array_is_list($data)) {
            $entries = $data;
        } else {
            $error = 'Directory data file is missing the expected entries list.';
            return [];
        }

        $normalised = [];
        foreach ($entries as $entry) {
            if (!is_array($entry)) {
                continue;
            }

            $row = [];
            foreach ($entry as $key => $value) {
                if (!is_string($key)) {
                    continue;
                }

                if (is_scalar($value) || $value === null) {
                    $row[$key] = trim((string) $value);
                }
            }

            if ($row !== []) {
                $normalised[] = $row;
            }
        }

        if ($normalised === []) {
            $error = 'No directory entries were found in the data file.';
        }

        return $normalised;
    }
}

if (!function_exists('array_is_list')) {
    /**
     * Polyfill for array_is_list in PHP < 8.1.
     *
     * @param array<mixed> $array
     */
    function array_is_list(array $array): bool
    {
        if ($array === []) {
            return true;
        }

        $nextKey = 0;
        foreach ($array as $key => $_) {
            if ($key !== $nextKey) {
                return false;
            }
            $nextKey++;
        }

        return true;
    }
}