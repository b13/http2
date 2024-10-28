<?php

namespace B13\Http2\Event;

class ProcessResourcesEvent
{
    private array $resources;

    public function __construct(array $resources)
    {
        $this->resources = $resources;
    }

    public function getResources(): array
    {
        return $this->resources;
    }

    public function setResources(array $resources): void
    {
        $this->resources = $resources;
    }
}
