<?php

declare(strict_types=1);

namespace PhpSchool\Website\Online;

class ComposerPackageAllowlist
{
    public const DEFAULT_PACKAGE_LIST = __DIR__ . '/../../var/packages.json';

    private const NAME_PATTERN = '#^[a-z0-9]([_.-]?[a-z0-9]+)*/[a-z0-9](([_.]|-{1,2})?[a-z0-9]+)*$#';
    private const VERSION_PATTERN = '#^[a-zA-Z0-9.*^~<>=!|,@ -]{1,64}$#';

    /**
     * @var array<string, int>|null
     */
    private ?array $packages = null;

    public function __construct(
        private readonly string $packageListPath = self::DEFAULT_PACKAGE_LIST,
        private readonly int $maxPackages = 10,
    ) {}

    /**
     * @return array<string, string>
     */
    public function validate(mixed $dependencies): array
    {
        if (!is_array($dependencies)) {
            throw new \RuntimeException('Invalid composer dependencies');
        }

        if (count($dependencies) > $this->maxPackages) {
            throw new \RuntimeException(sprintf('A maximum of %d composer dependencies are allowed', $this->maxPackages));
        }

        $require = [];
        foreach ($dependencies as $dependency) {
            if (!is_array($dependency) || !is_string($dependency['name'] ?? null) || !is_string($dependency['version'] ?? null)) {
                throw new \RuntimeException('Invalid composer dependencies');
            }

            $name = $dependency['name'];
            $version = $dependency['version'];

            if (1 !== preg_match(self::NAME_PATTERN, $name) || !$this->isKnownPackage($name)) {
                throw new \RuntimeException(sprintf('Composer package "%s" is not allowed', $name));
            }

            if (1 !== preg_match(self::VERSION_PATTERN, $version)) {
                throw new \RuntimeException(sprintf('Invalid version constraint for composer package "%s"', $name));
            }

            $require[$name] = $version;
        }

        return $require;
    }

    private function isKnownPackage(string $name): bool
    {
        if ($this->packages === null) {
            $contents = is_readable($this->packageListPath) ? file_get_contents($this->packageListPath) : false;
            $list = $contents !== false ? json_decode($contents, true) : null;
            $names = is_array($list) && is_array($list['packageNames'] ?? null) ? $list['packageNames'] : [];

            $this->packages = array_flip(array_filter($names, 'is_string'));
        }

        return isset($this->packages[$name]);
    }
}
