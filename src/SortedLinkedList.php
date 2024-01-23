<?php

namespace PolakJan\SortedLinkedList;

use Collator;
use Iterator;
use PolakJan\SortedLinkedList\ListElement;
use PolakJan\SortedLinkedList\SortedLinkedListInterface;

/**
 * vocabulary:
 *  - element - object of class ListElement containing a value and the pointer to the next element
 *  - value - just the value (int or string) within one element
 */
class SortedLinkedList implements SortedLinkedListInterface, Iterator
{
    public const SORT_ASC = 'asc';
    public const SORT_DESC = 'desc';

    protected string $order = self::SORT_ASC;

    // current length of the list
    protected int $length = 0;

    // first element in the list
    protected null|ListElement $head = null;

    // position of the current element in the list
    protected int $position = 0;

    // current element in the list or null if we're at the end of the list
    protected null|ListElement $current = null;

    // type of the inserted values
    protected null|string $value_type = null;

    // string comparison locale
    protected string $locale = 'en_US';

    // is the Collator from intl extension available
    protected ?bool $collator_available = null;

    // collator instance
    protected ?Collator $collator = null;

    /**
     * sets the locale for string comparisons
     *
     * @param string locale as per RFC4646
     * @return void
     */
    public function setLocale(string $locale): void
    {
        if (!$this->isCollatorAvailable()) {
            throw new \Exception('Collator not available. Is the `intl` extension installed?');
        }

        if ($this->locale !== $locale) {
            $this->locale = $locale;

            // clear the collator so that it can be created again
            // with the new locale
            $this->collator = null;

            if ($this->head) {
                $this->resort();
            }
        }
    }

    /**
     * sets the sorting order
     *
     * the only valid values are 'asc' or 'desc'
     *
     * @param string the order of sorting
     * @return void
     */
    public function setOrder(string $order): void
    {
        if ($order !== static::SORT_ASC && $order !== static::SORT_DESC) {
            throw new \InvalidArgumentException('Invalid value for order. Only \'asc\' or \'desc\' are allowed');
        }

        if ($this->order !== $order) {
            $this->order = $order;

            if ($this->head) {
                $this->resort();
            }
        }
    }

    /**
     * inserts a value into the list
     *
     * also resets the internal pointer so that the next call to current()
     * returns the head element's value
     *
     * @param int|string inserted value
     * @return int position where the value was inserted
     */
    public function insert(int|string $value): int
    {
        $type = gettype($value);

        if (null === $this->value_type) {
            $this->value_type = $type;
        } elseif ($type !== $this->value_type) {
            throw new \InvalidArgumentException('Can\'t insert value of type '.$type.' with values of type '.$this->value_type.' already present.');
        }

        $this->rewind();

        $predecessor = $this->findInsertionPredecessor($value, $position);

        $this->insertAfter($value, $predecessor);

        $this->length++;

        return $position;
    }

    /**
     * returns the current length of the list
     *
     * @return int the length of the list
     */
    public function length(): int
    {
        return $this->length;
    }

    /**
     * returns the value of the current element if there is one
     * or null if there is none
     *
     * necessary for implementing the Iterator interface
     *
     * @return null|int|string value of the current element or null
     */
    public function current(): null|int|string
    {
        return $this->current ? $this->current->value() : null;
    }

    /**
     * returns the key - position of the current element
     *
     * necessary for implementing the Iterator interface
     * can return position outside of the list boundaries if next()
     * is called too many times, disregarding the return of valid()
     *
     * @return int position of the current element in the list
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * moves the internal pointer to the next element
     *
     * necessary for implementing the Iterator interface
     *
     * @return void
     */
    public function next(): void
    {
        $this->nextElement();
    }

    /**
     * resets the internal pointer, allowing to start iterating
     * from the beginning again
     *
     * necessary for implementing the Iterator interface
     *
     * @return void
     */
    public function rewind(): void
    {
        $this->position = 0;

        $this->current = $this->head;
    }

    /**
     * determines whether the current internal pointer points to
     * an existing element
     *
     * necessary for implementing the Iterator interface
     *
     * @return bool is/not the internal pointer pointing to an existing element
     */
    public function valid(): bool
    {
        return $this->position <= $this->length - 1;
    }

    /**
     * seeks to a position and returns the value there
     *
     * if the value is not found, returns null
     * if the position is negative, seeks from the end
     *
     * @param int position where the value should be found
     * @return null|int|string returned value or null on
     */
    public function seek(int $position): null|int|string
    {
        $element = $this->seekElement($position);

        return $element ? $element->value() : null;
    }

    /**
     * removes an element from an arbitrary position in the list
     *
     * if the position is negative, removes from the end
     *
     * @param int position from which the element should be removed
     * @return null|int|string the removed element
     */
    public function remove(int $position): null|int|string
    {
        if ($position === 0) {
            return $this->shift();
        }

        if ($position < 0) {
            $position = $this->length + $position + 1;
        }

        $previous_element = $this->seekElement($position - 1);

        if ($previous_element && $element = $previous_element->next()) {
            $previous_element->setNext($element->next());

            $this->length--;

            return $element->value();
        }

        return null;
    }

    /**
     * shifts an element off the beginning of the list
     *
     * @return null|int|string the removed element
     */
    public function shift(): null|int|string
    {
        if (!$this->head) {
            return null;
        }

        $old_head = $this->head;

        $this->head = $this->head->next();

        $this->length--;

        return $old_head->value();
    }

    /**
     * pops an element off the end of the list
     *
     * @return null|int|string the removed element
     */
    public function pop(): null|int|string
    {
        if ($this->length <= 1) {
            return $this->shift();
        }

        return $this->remove($this->length - 1);
    }

    /**
     * converts the list to a numerically indexed array
     *
     * @return array the resulting array
     */
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

    /**
     * returns the element to which the internal pointer is currently pointing
     * or null on an empty list
     *
     * @return null|ListElement the current element or null on empty list
     */
    protected function currentElement(): null|ListElement
    {
        return $this->current;
    }

    /**
     * moves the internal pointer to the next element if there is one
     *
     * always raises $this->position which is necessary for Iterator implementation
     *
     * @return null|ListElement the new current element or null if there is no next element
     */
    protected function nextElement(): null|ListElement
    {
        // always try to raise this, necessary for Iterator implementation
        $this->position++;

        if (null === $this->current || null === $this->current->next()) {
            return null;
        }

        $this->current = $this->current->next();

        return $this->current;
    }

    /**
     * tries to find an element on an arbitrary position
     *
     * @param int position where to look for the element. Negative values
     *            seek from the end.
     * @return null|ListElement the found element or null if such a position
     *                          does not exist
     */
    protected function seekElement(int $position): null|ListElement
    {
        if ($position < 0) {
            $position = $this->length + $position;
        }

        if ($position < 0 || $position > $this->length - 1) {
            return null;
        }

        $element = $this->head;

        $i = 0;

        while ($element) {
            if ($i++ === $position) {
                break;
            }

            $element = $element->next();
        }

        return $element;
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

    /**
     * re-sort the values again, in place
     *
     * makes sense to call this only when sorting parameters (order, locale)
     * change
     *
     * @return void
     */
    protected function resort(): void
    {
        $this->head = $this->mergeSort($this->head);
    }

    /**
     * determines whether one value should be considered bigger or equal to another
     *
     * uses the internal settings of order and locale if necessary
     *
     * @param int|string first value
     * @param int|string second value
     * @return bool is/not the first value considered bigger or equal than the second
     */
    protected function biggerOrEqual(int|string $a, int|string $b): bool
    {
        if (is_string($a) && $this->isCollatorAvailable()) {
            $a = $this->getCollator()->compare($a, $b);
            $b = 0;
        }

        return $this->order === static::SORT_ASC
            ? $a >= $b
            : $a <= $b;
    }

    /**
     * checks if the Collator class from the `intl` extension is available
     *
     * remembers so that in the future the asking is faster
     *
     * @return bool is/not the Collator class available
     */
    protected function isCollatorAvailable(): bool
    {
        if ($this->collator_available === null) {
            $this->collator_available = extension_loaded('intl') && class_exists('Collator');
        }

        return $this->collator_available;
    }

    /**
     * instatiates and returns the Collator object necessary to compare localized
     * strings
     *
     * @return Collator the collator object
     */
    protected function getCollator(): Collator
    {
        if (null === $this->collator) {
            $this->collator = new Collator($this->locale);
        }

        return $this->collator;
    }

    /**
     * merge sort implementation for re-sorting the list
     *
     * @param null|ListElement first element of the list that needs sorting
     * @return null|ListElement first element of the sorted list
     */
    protected function mergeSort(null|ListElement $left_start): null|ListElement
    {
        if (null === $left_start || null === $left_start->next()) {
            return $left_start;
        }

        $middle = $this->mergeSortFindMiddle($left_start);

        // we end this list here and start a new one on the following element
        $right_start = $middle->next();
        $middle->setNext(null);

        $left = $this->mergeSort($left_start);
        $right = $this->mergeSort($right_start);

        return $this->mergeListsSorted($left, $right);
    }

    /**
     * find middle element of a list
     *
     * @param ListElement first element of the list
     * @return ListElement middle element of the list
     */
    protected function mergeSortFindMiddle(ListElement $from): ListElement
    {
        $slow = $from;
        $fast = $from;

        while (null !== ($fast_next = $fast->next()) && null !== ($fast_next_next = $fast_next->next())) {
            $slow = $slow->next();
            $fast = $fast_next_next;
        }

        return $slow;
    }

    /**
     * recursively merge two lists to find their elements sorted
     *
     * part of the merge sort implementation
     *
     * @param null|ListElement first element of first list to be merged
     * @param null|ListElement first element of second list to be merged
     * @return null|ListElement first element of the merged list
     */
    protected function mergeListsSorted(null|ListElement $left, null|ListElement $right): null|ListElement
    {
        $result = null;

        if (null === $left) return $right;
        if (null === $right) return $left;

        if ($this->biggerOrEqual($right->value(), $left->value())) {
            $result = $left;
            $result->setNext($this->mergeListsSorted($left->next(), $right));

        } else {
            $result = $right;
            $result->setNext($this->mergeListsSorted($left, $right->next()));
        }

        return $result;
    }
}