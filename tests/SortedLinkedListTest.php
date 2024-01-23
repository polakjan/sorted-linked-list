<?php

use PHPUnit\Framework\TestCase;
use PolakJan\SortedLinkedList\SortedLinkedList;

class SortedLinkedListTest extends TestCase
{
    public function testListObjectCanBeInstantiated()
    {
        $list = new SortedLinkedList;

        $this->assertInstanceOf(SortedLinkedList::class, $list);
    }

    public function testEmptyListYieldsEmptyArray()
    {
        $list = new SortedLinkedList;

        $this->assertSame([], $list->toArray());
    }

    public function testListToArrayContainsAllTheExpectedValues()
    {
        $values = [1, 3, 5, 8];

        $list = new SortedLinkedList;

        foreach ($values as $value) {
            $list->insert($value);
        }

        $this->assertEmpty(array_diff($values, $list->toArray()));
        $this->assertEmpty(array_diff($list->toArray(), $values));
    }

    public function testListCorrectlySortsIntegers()
    {
        $values = [8, 3, 1, 5];

        $list = new SortedLinkedList;

        foreach ($values as $value) {
            $list->insert($value);
        }

        $this->assertSame([1, 3, 5, 8], $list->toArray());
    }

    public function testListCorrectlySortsIntegersInDescendingOrder()
    {
        $values = [8, 3, 1, 5];

        $list = new SortedLinkedList;
        $list->setOrder('desc');

        foreach ($values as $value) {
            $list->insert($value);
        }

        $this->assertSame([8, 5, 3, 1], $list->toArray());
    }

    public function testListCorrectlyResortsIntegersAfterOrderChange()
    {
        $values = [8, 3, 1, 5];

        $list = new SortedLinkedList;
        $list->setOrder('asc');

        foreach ($values as $value) {
            $list->insert($value);
        }

        $this->assertSame([1, 3, 5, 8], $list->toArray());

        $list->setOrder('desc');

        $this->assertSame([8, 5, 3, 1], $list->toArray());
    }

    public function testListCorrectlySortsStrings()
    {
        $values = ['h', 'c', 'a', 'e'];

        $list = new SortedLinkedList;

        foreach ($values as $value) {
            $list->insert($value);
        }

        $this->assertSame(['a', 'c', 'e', 'h'], $list->toArray());
    }

    public function testListCorrectlySortsStringsInDescendingOrder()
    {
        $values = ['h', 'c', 'a', 'e'];

        $list = new SortedLinkedList;
        $list->setOrder('desc');

        foreach ($values as $value) {
            $list->insert($value);
        }

        $this->assertSame(['h', 'e', 'c', 'a'], $list->toArray());
    }

    public function testListCorrectlyResortsStringsAfterOrderChange()
    {
        $values = ['h', 'c', 'a', 'e'];

        $list = new SortedLinkedList;
        $list->setOrder('asc');

        foreach ($values as $value) {
            $list->insert($value);
        }

        $this->assertSame(['a', 'c', 'e', 'h'], $list->toArray());

        $list->setOrder('desc');

        $this->assertSame(['h', 'e', 'c', 'a'], $list->toArray());
    }

    /**
     * @requires extension intl
     */
    public function testListCorrectlySortsLocalizedStrings()
    {
        $values = [
            'ä',
            'z',
            'ch',
            'c',
            'd'
        ];

        $expected_orderings = [
            'cs_CZ' => ['ä', 'c', 'd', 'ch', 'z'],
            'en_US' => ['ä', 'c', 'ch', 'd', 'z'],
            'sv_SE' => ['c', 'ch', 'd', 'z', 'ä']
        ];

        foreach ($expected_orderings as $locale => $expected_ordering) {
            $list = new SortedLinkedList;

            $list->setLocale($locale);

            foreach ($values as $value) {
                $list->insert($value);
            }

            $this->assertSame($expected_ordering, $list->toArray());
        }
    }

    public function testListCorrectlyResortsStringsAfterLocaleChange()
    {
        $values = [
            'ä',
            'z',
            'ch',
            'c',
            'd'
        ];

        $list = new SortedLinkedList;
        $list->setLocale('en_US');

        foreach ($values as $value) {
            $list->insert($value);
        }

        $this->assertSame(['ä', 'c', 'ch', 'd', 'z'], $list->toArray());

        $list->setLocale('cs_CZ');

        $this->assertSame(['ä', 'c', 'd', 'ch', 'z'], $list->toArray());

        $list->setLocale('sv_SE');

        $this->assertSame(['c', 'ch', 'd', 'z', 'ä'], $list->toArray());
    }

    public function testInsertingInvalidValueTypeThrowsException()
    {
        $list = new SortedLinkedList;

        $this->expectException('TypeError');

        $list->insert([]); // intentionally wrong
    }

    public function testInsertingValuesOfDifferentTypesThrowsException()
    {
        $list = new SortedLinkedList;
        $list->insert(123);

        $this->expectException('InvalidArgumentException');

        $list->insert('abc');
    }

    public function testSeekingToExistingPositionReturnsValue()
    {
        $values = [8, 3, 1, 5];

        $list = new SortedLinkedList;
        $list->setOrder('asc');

        foreach ($values as $value) {
            $list->insert($value);
        }

        $this->assertSame(5, $list->seek(2));
    }

    public function testSeekingToNegativePositionSeeksFromTheEnd()
    {
        $values = [8, 3, 1, 5];

        $list = new SortedLinkedList;
        $list->setOrder('asc');

        foreach ($values as $value) {
            $list->insert($value);
        }

        $this->assertSame(8, $list->seek(-1));
        $this->assertSame(5, $list->seek(-2));
        $this->assertSame(1, $list->seek(-4));
        $this->assertSame(null, $list->seek(-5));
    }

    public function testSeekingToNonexistingPositionReturnsNull()
    {
        $values = [8, 3, 1, 5];

        $list = new SortedLinkedList;

        $this->assertSame(null, $list->seek(0));

        foreach ($values as $value) {
            $list->insert($value);
        }

        $this->assertSame(null, $list->seek(4));
    }

    public function testRemovingElementWorks()
    {
        $values = [8, 3, 1, 5];

        $list = new SortedLinkedList;

        foreach ($values as $value) {
            $list->insert($value);
        }

        $removed = $list->remove(2);

        $this->assertSame([1, 3, 8], $list->toArray());
        $this->assertSame(5, $removed);
        $this->assertSame(3, $list->length());
    }

    public function testRemovingHeadElementWorks()
    {
        $values = [8, 3, 1, 5];

        $list = new SortedLinkedList;

        foreach ($values as $value) {
            $list->insert($value);
        }

        $removed = $list->remove(0);

        $this->assertSame([3, 5, 8], $list->toArray());
        $this->assertSame(1, $removed);
        $this->assertSame(3, $list->length());
    }

    public function testShiftingElementsWorks()
    {
        $values = [8, 3, 1, 5];

        $list = new SortedLinkedList;

        foreach ($values as $value) {
            $list->insert($value);
        }

        $shifted = $list->shift();

        $this->assertSame([3, 5, 8], $list->toArray());
        $this->assertSame(1, $shifted);
        $this->assertSame(3, $list->length());

        $list->shift();

        $list->shift();

        $last_shifted = $list->shift();
        $this->assertSame(8, $last_shifted);
        $this->assertSame(0, $list->length());
    }

    public function testPoppingElementsWorks()
    {
        $values = [8, 3, 1, 5];

        $list = new SortedLinkedList;

        foreach ($values as $value) {
            $list->insert($value);
        }

        $popped = $list->pop();

        $this->assertSame([1, 3, 5], $list->toArray());
        $this->assertSame(8, $popped);
        $this->assertSame(3, $list->length());

        $list->pop();

        $list->pop();

        $last_popped = $list->pop();
        $this->assertSame(1, $last_popped);
        $this->assertSame(0, $list->length());
    }

    public function testListCanBeUsedAsTraversable()
    {
        $values = [8, 3, 1, 5];

        $list = new SortedLinkedList;

        foreach ($values as $value) {
            $list->insert($value);
        }

        $keys_from_foreach = [];
        $values_from_foreach = [];

        foreach ($list as $key => $value) {
            $keys_from_foreach[] = $key;
            $values_from_foreach[] = $value;
        }

        $this->assertSame([0, 1, 2, 3], $keys_from_foreach);
        $this->assertSame([1, 3, 5, 8], $values_from_foreach);

        // once more to test the functionality of rewind

        $keys_from_foreach = [];
        $values_from_foreach = [];

        foreach ($list as $key => $value) {
            $keys_from_foreach[] = $key;
            $values_from_foreach[] = $value;
        }

        $this->assertSame([0, 1, 2, 3], $keys_from_foreach);
        $this->assertSame([1, 3, 5, 8], $values_from_foreach);
    }
}