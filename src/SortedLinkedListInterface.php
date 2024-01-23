<?php

namespace PolakJan\SortedLinkedList;

interface SortedLinkedListInterface
{
    /**
     * sets the locale for string comparisons
     *
     * @param string the new locale
     * @return void
     */
    public function setLocale(string $locale): void;

    /**
     * sets the sorting order
     *
     * @param string the order of sorting
     * @return void
     */
    public function setOrder(string $order): void;

    /**
     * inserts a value into the list
     *
     * @param int|string inserted value
     * @return int position where the value was inserted
     */
    public function insert(int|string $value): int;

    /**
     * returns the current length of the list
     *
     * @return int the length of the list
     */
    public function length(): int;

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
     * removes an element from an arbitrary position in the list
     *
     * @param int position from which the element should be removed
     * @return null|int|string the removed element
     */
    public function remove(int $position): null|int|string;

    /**
     * shifts an element off the beginning of the list
     *
     * @return null|int|string the removed element
     */
    public function shift(): null|int|string;

    /**
     * pops an element off the end of the list
     *
     * @return null|int|string the removed element
     */
    public function pop(): null|int|string;

    /**
     * exports the linked values into a standard, numerically
     * indexed array
     *
     * @return array the resulting array
     */
    public function toArray(): array;
}