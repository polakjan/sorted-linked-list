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
