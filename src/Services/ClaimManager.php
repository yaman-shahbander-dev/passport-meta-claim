<?php

namespace PassportMetaClaim\Services;

use Illuminate\Support\Str;
use ReflectionClass;
use InvalidArgumentException;

/**
 * Manages claim classes dynamically for PassportMetaClaim.
 */
class ClaimManager
{
    /**
     * Stores discovered claims.
     *
     * @var array<string, mixed>
     */
    protected array $claims = [];

    /**
     * Initializes the claim manager and discovers available claims.
     */
    public function __construct()
    {
        $this->discoverClaims();
    }

    /**
     * Retrieves all discovered claims.
     *
     * @return array<string, mixed> Associative array of claims
     */
    public function getClaims(): array
    {
        return $this->claims;
    }

    /**
     * Discovers and loads valid claim classes.
     *
     * @return void
     */
    protected function discoverClaims(): void
    {
        $config = config('passport-meta-claim');

        $path = $config['path'];
        $suffix = $config['suffix'];

        // Ensure the claims directory exists
        if (! is_dir($path)) {
            mkdir($path, 0755, true);
        }

        // Scan directory for claim files
        foreach (scandir($path) as $file) {
            if (is_file("$path/$file")) {
                $this->processClaimFile($path, $file, $suffix);
            }
        }
    }

    /**
     * Processes an individual claim file to determine its validity.
     *
     * @param string $path   Path of the claims directory
     * @param string $file   Filename to process
     * @param string $suffix Expected suffix for valid claim classes
     *
     * @return void
     */
    protected function processClaimFile(string $path, string $file, string $suffix): void
    {
        if (! str_ends_with($file, '.php')) {
            return;
        }

        $className = Str::before($file, '.php');
        if (! str_ends_with($className, $suffix)) {
            return;
        }

        $fullClass = $this->getClassNamespace($path, $file) . '\\' . $className;

        if ($this->isValidClaimClass($fullClass)) {
            $claimName = Str::snake(Str::before($className, $suffix));

            $this->claims[$claimName] = app($fullClass);
        }
    }

    /**
     * Extracts the namespace from a claim file.
     *
     * @param string $path Path of the claims directory
     * @param string $file Filename to extract namespace from
     *
     * @throws InvalidArgumentException If namespace is not found
     *
     * @return string Extracted namespace
     */
    protected function getClassNamespace(string $path, string $file): string
    {
        $content = file_get_contents("$path/$file");

        preg_match('/namespace\s+(.+?);/s', $content, $matches);

        if (! isset($matches[1])) {
            throw new InvalidArgumentException("Namespace not found in $file");
        }

        return $matches[1];
    }

    /**
     * Validates if the given class is a proper claim class.
     *
     * @param string $class Fully qualified class name
     *
     * @return bool Whether the class is a valid claim
     */
    protected function isValidClaimClass(string $class): bool
    {
        if (! class_exists($class)) {
            return false;
        }

        $reflection = new ReflectionClass($class);

        return $reflection->isInstantiable() && $reflection->hasMethod('__invoke');
    }
}