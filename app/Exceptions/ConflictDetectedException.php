<?php

namespace App\Exceptions;

use Exception;

class ConflictDetectedException extends Exception
{
    protected int $currentVersion;
    protected int $yourVersion;
    protected array $diff;

    public function __construct(
        int $currentVersion,
        int $yourVersion,
        array $diff = [],
        string $message = "Conflict detected: The resource has been modified by another user",
        int $code = 409
    ) {
        parent::__construct($message, $code);
        $this->currentVersion = $currentVersion;
        $this->yourVersion = $yourVersion;
        $this->diff = $diff;
    }

    public function getCurrentVersion(): int
    {
        return $this->currentVersion;
    }

    public function getYourVersion(): int
    {
        return $this->yourVersion;
    }

    public function getDiff(): array
    {
        return $this->diff;
    }

    public function toArray(): array
    {
        return [
            'current_version' => $this->currentVersion,
            'your_version' => $this->yourVersion,
            'details' => $this->diff,
        ];
    }
}
