<?php
namespace App\Services\PlatformValidators;

class PlatformContentValidatorFactory
{
    /**
     * Return a validator instance based on platform type.
     *
     * @param string $platformType
     * @return PlatformContentValidatorInterface|null
     */
    public static function make(string $platformType): ?PlatformContentValidatorInterface
    {
        return match ($platformType) {
             'twitter' => new TwitterContentValidator(),
             'instagram' => new InstagramContentValidator(),
             'linkedin' => new LinkedInContentValidator(),
            default => null,
        };
    }
}