<?php
namespace App\Services\PlatformValidators;

class InstagramContentValidator implements PlatformContentValidatorInterface
{
    protected int $maxLength = 2200; // حد النصوص في إنستغرام
    protected string $errorMessage = '';

    public function validate(string $content): bool
    {
        if (strlen($content) > $this->maxLength) {
            $this->errorMessage = "Content exceeds the {$this->maxLength} characters limit for Instagram.";
            return false;
        }
        return true;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}