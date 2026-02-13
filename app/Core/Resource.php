<?php

namespace App\Core;

/**
 * Base API Resource for JSON transformations
 */
abstract class Resource
{
    protected $resource;

    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    abstract public function toArray(): array;

    public static function make($resource): self
    {
        return new static($resource);
    }

    public static function collection(array $resources): array
    {
        return array_map(fn($item) => (new static($item))->toArray(), $resources);
    }

    public function response(): string
    {
        header('Content-Type: application/json');
        return json_encode([
            'data' => $this->toArray()
        ]);
    }
}
