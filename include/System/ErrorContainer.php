<?php

namespace MerapiPanel\System;

use Il4mb\BlockNode\Node;
use Il4mb\BlockNode\TextNode;
use Symfony\Component\Filesystem\Path;
use Throwable;

class ErrorContainer
{
    private string $message;
    private string $file;
    private int $line;
    private array $stackTrace;
    private string $name;
    private int $code;

    function getCode()
    {
        return $this->code;
    }


    public function __construct(Throwable|array $t)
    {
        if ($t instanceof Throwable) {
            $this->message    = $t->getMessage();
            $this->file       = $t->getFile();
            $this->line       = $t->getLine();
            $this->stackTrace = $t->getTrace();
            $this->name  = get_class($t);
            $this->code  = $t->getCode();
        } else {
            $this->message = $t['message'] ?? "";
            $this->file    = $t['file'] ?? "";
            $this->line    = $t['line'] ?? "";
            $this->stackTrace = $t['stackTrace'] ?? [];
            $this->name  = $t['name'] ?? "";
            $this->code  = $t['code'] ?? 500;
        }
    }

    function getMessage()
    {
        return $this->message;
    }

    function getFile()
    {
        return $this->file;
    }

    function getLine()
    {
        return $this->line;
    }

    function getStackTrace()
    {
        return $this->stackTrace;
    }


    function getTitle()
    {
        return $this->name;
    }

    function getSnippet()
    {
        $file     = $this->file;
        $line     = $this->line;
        $maxLines = 15;

        if (!file_exists($file)) {
            return 'File not found: ' . $file;
        }
        $half = intval($maxLines / 2);
        $advantage = 0;
        if ($half < ($maxLines / 2)) {
            $advantage += (($maxLines / 2) - $half) * 2;
        }
        $lines = file($file);
        $start = max(0, ($line - $half) - $advantage);
        $end = min(count($lines), $line + $half);

        $snippet = new Node(
            tagName: "code",
            attribute: [
                "class" => "language-" . Path::getExtension($file, true),
                "id" => "error-snippet",
                "error-line" => $line
            ]
        );
        for ($i = $start; $i < $end; $i++) {
            if ($i == 0) continue;
            $lineNumber = $i + 1;
            $lineContent = htmlentities($lines[$i]);
            $snippet->append(new TextNode("$lineNumber $lineContent"));
        }
        return $snippet;
    }

    public static function create($name, $message, $file, $line)
    {
        return new static([
            'name' => $name,
            'message' => $message,
            'file' => $file,
            'line' => $line
        ]);
    }
}
