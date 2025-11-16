<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Provides custom Twig functions for the application.
 *
 * Currently, this extension exposes the app_version() function, which retrieves
 * the application version from composer.json. This allows templates to easily
 * display the current version of the application.
 *
 * Usage in Twig:
 *   {{ app_version() }}
 */
class AppExtension extends AbstractExtension
{
    /**
     * Returns a list of custom Twig functions provided by this extension.
     *
     * @return TwigFunction[]
     *   Array of TwigFunction instances, including 'app_version' for retrieving the application version.
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('app_version', [$this, 'getAppVersion'])
        ];
    }

    /**
     * Retrieves the application version from composer.json.
     *
     * @return string
     *   The application version as defined in composer.json, or 'unknown' if not found.
     */
    public function getAppVersion(): string
    {
        $composerJsonPath = __DIR__ . '/../../composer.json';
        if (file_exists($composerJsonPath)) {
            $composerData = json_decode(file_get_contents($composerJsonPath), true);
            return $composerData['version'] ?? 'unknown';
        }
        return 'unknown';
    }
}
