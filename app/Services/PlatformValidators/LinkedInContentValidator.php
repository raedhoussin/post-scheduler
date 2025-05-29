<?php
namespace App\Services\PlatformValidators;

class LinkedInContentValidator implements PlatformContentValidatorInterface
{
    protected int $maxLength = 1300; // حد النصوص في لينكدإن
    protected string $errorMessage = '';

    public function validate(string $content): bool
    {
        if (strlen($content) > $this->maxLength) {
            $this->errorMessage = "Content exceeds the {$this->maxLength} characters limit for LinkedIn.";
            return false;
        }
        return true;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}