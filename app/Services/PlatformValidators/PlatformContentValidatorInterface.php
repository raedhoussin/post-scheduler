<?php
namespace App\Services\PlatformValidators;

interface PlatformContentValidatorInterface
{
    /**
     * Validate the content according to platform rules.
     *
     * @param string $content
     * @return bool
     */
    public function validate(string $content): bool;

    /**
     * Get the validation error message if validation failed.
     *
     * @return string
     */
    public function getErrorMessage(): string;
}