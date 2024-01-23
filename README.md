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

#### Seeking to a value on a specific position

```php
$value = $list->seek(3);

// 2
```

## TODO:

* resort list - might require separate list driver/container
* implement Iterable, Traversable, ArrayAccess
    - some of them won't be possible because we can't insert elements at arbitraty positions