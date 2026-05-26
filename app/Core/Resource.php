<?php
/**
 * Resource — JSON API dönüşümleri için base.
 *
 * Kullanım:
 *   class UserResource extends Resource {
 *       public function toArray(): array { return ['id'=>$this->resource->id, ...]; }
 *   }
 *   return UserResource::make($user)->respond();
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Core;

abstract class Resource
{
    protected mixed $resource;

    public function __construct(mixed $resource)
    {
        $this->resource = $resource;
    }

    abstract public function toArray(): array;

    public static function make(mixed $resource): static
    {
        return new static($resource);
    }

    /** @param iterable $items */
    public static function collection(iterable $items): array
    {
        $out = [];
        foreach ($items as $item) {
            $out[] = (new static($item))->toArray();
        }
        return $out;
    }

    public function respond(int $status = 200): void
    {
        (new Response())->json(['data' => $this->toArray()], $status);
    }
}
