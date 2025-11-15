<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppVersionExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('app_version', [$this, 'getAppVersion'])
        ];
    }

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

