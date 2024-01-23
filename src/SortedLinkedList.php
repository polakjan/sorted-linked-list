<?php

namespace PolakJan\SortedLinkedList;

use Collator;
use PolakJan\SortedLinkedList\ListElement;
use PolakJan\SortedLinkedList\SortedLinkedListInterface;

/**
 * volabulary:
 *  - element - object of class ListElement containing a value and the pointer to the next element
 *  - value - just the value (int or string) within one element
 */
class SortedLinkedList implements SortedLinkedListInterface
{
    // first element in the list
    protected null|ListElement $head = null;

    // pointer to current element in the list or null if we're at the end of the list
    protected null|ListElement $current = null;

    // type of the inserted values
    protected null|string $value_type = null;

    // string comparison locale
    protected string $locale = 'en_US';

    // is the Collator from intl extension available
    protected ?bool $collator_available = null;

    // collator instance
    protected ?Collator $collator = null;


    public function setLocale(string $locale): void
    {
        if (!$this->isCollatorAvailable()) {
            throw new \Exception('Collator not available. Is the `intl` extension installed?');
        }

        if ($this->locale !== $locale) {

            if ($this->head) {
                throw new \Exception('Unable to set locale on non-empty list.');
            }

            $this->locale = $locale;

            // clear the collator so that it can be created again
            // with the new locale
            $this->collator = null;
        }
    }

    public function insert(int|string $value): int
    {
        $type = gettype($value);

        if (null === $this->value_type) {
            $this->value_type = $type;
        } elseif ($type !== $this->value_type) {
            throw new \InvalidArgumentException('Can\'t insert value of type '.$type.' with values of type '.$this->value_type.' already present.');
        }

        $this->reset();

        $predecessor = $this->findInsertionPredecessor($value, $position);

        $this->insertAfter($value, $predecessor);

        return $position;
    }

    public function reset()
    {
        $this->current = $this->head;
    }

    public function current(): null|int|string
    {
        return $this->current ? $this->current->value() : null;
    }

    public function next(): null|int|string
    {
        if (null === $this->nextElement()) {
            return null;
        }

        return $this->current->value();
    }

    public function seek(int $position): null|int|string
    {
        return null;
    }

    public function toArray(): array
    {
        $array = [];

        $element = $this->head;

        while ($element) {
            $array[] = $element->value();

            $element = $element->next();
        }

        return $array;
    }

    protected function currentElement(): null|ListElement
    {
        return $this->current;
    }

    protected function nextElement(): null|ListElement
    {
        if (null === $this->current || null === $this->current->next()) {
            return null;
        }

        $this->current = $this->current->next();

        return $this->current;
    }

    protected function findInsertionPredecessor(int|string &$value, ?int &$position = 0): null|ListElement
    {
        $previous = null;
        $position = 0;

        $element = $this->head;

        do {
            if (!$element || $this->biggerOrEqual($element->value(), $value)) {
                break;
            }

            $position++;
            $previous = $element;
            $element = $element->next();
        }
        while ($element);

        return $previous;
    }

    /**
     * inserts a given value as a new element after
     * the reference element
     *
     * @param int|string value to be inserted
     * @param null|ListElement element after which the new element should be inserted
     */
    protected function insertAfter(int|string $value, null|ListElement $reference_element): void
    {
        if ($reference_element) {
            $next = $reference_element->next();
        } else {
            $next = $this->head;
        }

        $element = new ListElement(
            $value,
            $next
        );

        if ($reference_element) {
            $reference_element->setNext($element);
        } else {
            $this->head = $element;
            $this->current = $element;
        }
    }

    protected function resort()
    {
        // $value = $this->head;

        // do {

        // }
    }

    protected function biggerOrEqual(int|string $a, int|string $b): bool
    {
        if (is_string($a) && $this->isCollatorAvailable()) {
            return $this->getCollator()->compare($a, $b) >= 0;
        }

        return $a >= $b;
    }

    protected function isCollatorAvailable()
    {
        if ($this->collator_available === null) {
            $this->collator_available = extension_loaded('intl') && class_exists('Collator');
        }

        return $this->collator_available;
    }

    protected function getCollator(): Collator
    {
        if (null === $this->collator) {
            $this->collator = new Collator($this->locale);
        }

        return $this->collator;
    }
}