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

        $list_as_array = $list->toArray();

        $this->assertEmpty(array_diff($values, $list_as_array));
        $this->assertEmpty(array_diff($list_as_array, $values));
    }

    public function testListCorrectlySortsIntegers()
    {
        $values = [8, 3, 1, 5];

        $list = new SortedLinkedList;

        foreach ($values as $value) {
            $list->insert($value);
        }

        $list_as_array = $list->toArray();

        $this->assertSame([1, 3, 5, 8], $list_as_array);
    }

    public function testListCorrectlySortsStrings()
    {
        $values = ['h', 'c', 'a', 'e'];

        $list = new SortedLinkedList;

        foreach ($values as $value) {
            $list->insert($value);
        }

        $list_as_array = $list->toArray();

        $this->assertSame(['a', 'c', 'e', 'h'], $list_as_array);
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

            $list_as_array = $list->toArray();

            $this->assertSame($expected_ordering, $list_as_array);
        }
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
}