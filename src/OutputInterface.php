<?php

namespace App;

class OutputInterface
{
    public function write(string $message, bool $prefix = true): void
    {
        echo ($prefix ? $this->getMessagePrefix() : '') . $message;
    }

    public function writeln(string $message, $prefix = true): void
    {
        echo ($prefix ? $this->getMessagePrefix() : '') . $message . "\n";
    }

    private function getMessagePrefix(): string
    {
        $dateTime = new \DateTime();

        return '[' . $dateTime->format('Y-m-d') . '] ';
    }
}
