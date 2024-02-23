<?php

namespace MerapiPanel\Utility;

class Util
{

    public static function uniq($lenght = 13)
    {

        // uniqid gives 13 chars, but you could adjust it to your needs.
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($lenght / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
        } else {
            throw new \Exception("no cryptographically secure random function available");
        }
        return substr(bin2hex($bytes), 0, $lenght);
    }


    public static function getNamespaceFromFile($filePath)
    {
        $fileContent = file_get_contents($filePath);
        if ($fileContent === false) {
            throw new \Exception("Unable to read the file: $filePath");
        }

        // Regular expression for matching the namespace line
        $pattern = '/^namespace\s+([^;]+);/m';

        if (preg_match($pattern, $fileContent, $matches)) {
            return $matches[1]; // The first capturing group contains the namespace
        }

        return null; // Return null if no namespace is found
    }



    public static function getClassNameFromFile($filePath)
    {

        $fileContent = file_get_contents($filePath);
        if ($fileContent === false) {
            throw new \Exception("Unable to read the file: $filePath");
        }

        // Regular expressions for matching the namespace and class name
        $namespacePattern = '/^namespace\s+([^;]+);/m';
        $classPattern = '/^class\s+([a-zA-Z0-9_]+)/m';

        // Match namespace
        if (preg_match($namespacePattern, $fileContent, $namespaceMatches)) {
            $namespace = trim($namespaceMatches[1]);
        } else {
            $namespace = null;
        }

        // Match class name
        if (preg_match($classPattern, $fileContent, $classMatches)) {
            $className = trim($classMatches[1]);
        } else {
            $className = null;
        }

        // Combine namespace and class name
        if ($namespace !== null && $className !== null) {
            $fullyQualifiedName = $namespace . '\\' . $className;
        } else {
            $fullyQualifiedName = null;
        }

        return $fullyQualifiedName;
    }








    public static function cleanHtmlString($htmlString, $allowedTags = ['i', 'b', 'ul', 'ol', 'li', 'br', 'span', 'pre', 'strong', 'em', 'p', 'code', 'pre', 'hr'])
    {
        // Create a new DOMDocument and load the HTML string into it
        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($htmlString, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // Iterate over all nodes
        $xpath = new \DOMXPath($dom);
        foreach ($xpath->query('//*') as $node) {
            // If the node's tag name is not in the list of allowed tags, remove the node
            if (!in_array($node->nodeName, $allowedTags)) {
                // Replace the node with its own children
                while ($node->childNodes->length > 0) {
                    $child = $node->childNodes->item(0);
                    $node->parentNode->insertBefore($child, $node);
                }
                $node->parentNode->removeChild($node);
            }
        }

        // Return the cleaned HTML string
        return $dom->saveHTML();
    }


    public static function timeAgo(string|int $timestamp)
    {
        $timeAgo = gettype($timestamp) === 'string' ? strtotime($timestamp) : $timestamp;
        $currentTime = time();
        $timeDifference = $currentTime - $timeAgo;
        $seconds = $timeDifference;

        // Time intervals in seconds
        $minute = 60;
        $hour = 3600;
        $day = 86400;
        $week = 604800;
        $month = 2629440; // Average month in seconds (30.44 days)
        $year = 31553280; // Average year in seconds (365.24 days)

        if ($seconds <= 60) {
            return "Just now";
        } else if ($minute <= $seconds && $seconds < $hour) {
            $minutes = round($seconds / $minute);
            return "$minutes minutes ago";
        } else if ($hour <= $seconds && $seconds < $day) {
            $hours = round($seconds / $hour);
            return "$hours hours ago";
        } else if ($day <= $seconds && $seconds < $week) {
            $days = round($seconds / $day);
            return "$days days ago";
        } else if ($week <= $seconds && $seconds < $month) {
            $weeks = round($seconds / $week);
            return "$weeks weeks ago";
        } else if ($month <= $seconds && $seconds < $year) {
            $months = round($seconds / $month);
            return "$months months ago";
        } else {
            $years = round($seconds / $year);
            return "$years years ago";
        }
    }
}
