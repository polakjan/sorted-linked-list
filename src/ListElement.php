<?php

namespace PolakJan\SortedLinkedList;

class ListElement
{
    protected null|int|string $value = null;

    protected ?ListElement $next = null;

    public function __construct(int|string $value, null|ListElement $next = null)
    {
        $this->value = $value;

        $this->next = $next;
    }

    public function value()
    {
        return $this->value;
    }

    public function next()
    {
        return $this->next;
    }

    public function setNext(null|ListElement $next)
    {
        $this->next = $next;
    }
}