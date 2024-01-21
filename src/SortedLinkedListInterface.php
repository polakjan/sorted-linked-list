<?php

namespace PolakJan\SortedLinkedList;

interface SortedLinkedListInterface
{
    /**
     * inserts a value into the list
     *
     * also resets the pointer so that the next call to current()
     * returns the head element's value
     *
     * @param int|string inserted value
     * @return int position where the value was inserted
     */
    public function insert(int|string $value): int;

    /**
     * seeks to a position and returns the value there
     *
     * if the value is not found, returns null
     *
     * @param int position where the value should be found
     * @return null|int|string returned value or null on
     */
    public function seek(int $position): null|int|string;

    /**
     * exports the linked values into a standard, numerically
     * indexed array
     *
     * @return array the resulting array
     */
    public function toArray(): array;
}