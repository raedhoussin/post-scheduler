<?php
namespace App\Services\PlatformValidators;

class TwitterContentValidator implements PlatformContentValidatorInterface
{
    protected int $maxLength = 280;
    protected string $errorMessage = '';

    public function validate(string $content): bool
    {
        if (strlen($content) > $this->maxLength) {
            $this->errorMessage = "Content exceeds the {$this->maxLength} characters limit for Twitter.";
            return false;
        }
        return true;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}