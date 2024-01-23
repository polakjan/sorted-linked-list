# SortedLinkList

A linked list library that keeps values sorted.

### Expected usage:

```php
use PolakJan\SortedLinkedList\SortedLinkedList;
```

#### Inserting a value:

```php
$list = new SortedLinkedList;
$list->insert(0);
$list->insert(1);
$list->insert(3);
$list->insert(1);
$list->insert(2);

// the list now contains values (in order) 0, 1, 1, 2, 3
```

#### Setting the order:

The order can be set to `asc` or `desc` anytime.

```php
$list->setOrder('desc');

// the list now contains values (in order) 3, 2, 1, 1, 0
```

#### Changing the locale for string comparison:

The locale (as per FRC 4646) defines how string values would be compared.

For string comparison to work the `intl` extension must be installed.

```php
$list->setLocale('fr_FR');
```

#### Getting the length of the list:

```php
$list->length();
```

#### Seeking to a value on a specific position:

```php
$value = $list->seek(1);

// 2
```

#### Removing an element from a position in the list:

```php
$list->remove(2);

// the list now contains values (in order) 3, 2, 1, 0
```

#### Removing an element from the beginning of the list:

```php
$list->shift();

// the list now contains values (in order) 2, 1, 0
```

#### Removing an element from the end of the list:

```php
$list->pop();

// the list now contains values (in order) 2, 1
```

#### Getting the list as an array:

```php
$array = $list->toArray();

// [2, 1]
```

#### Traversing the list with a foreach loop:

```php
foreach ($list as $i => $value) {
    echo $i . ':' . $value . ", ";
}

// 0:2, 1:1
```