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
}