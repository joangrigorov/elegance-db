### Welcome to the Elegance Database Wrapper 

# What is this?
Elegance_Db is a PHP class built over the [pdo_mysql](http://php.net/manual/en/ref.pdo-mysql.php) extension 
and It's aimed to provide a simple API that supports the most basic(and most frequently used) operations with
MySQL.

Any similarities with Zend_Db are not a accidental. You can look at Elegance_Db as a light version of Zend_Db_Adapter_Pdo_Mysql.
Elegance_Db is inspired by Zend_Db, but It's designed for small and simple projects. 

# How is Elegance_Db better?
It's not. It's just lighter and smaller than the most libraries.

There are many database libraries (AdoDB, Zend_Db, Doctrine, Propel) which are great, but as I said before, 
Elegance_Db is very small, very simple and yet - very handy. 

# Configuration

## How to setup Elegance_Db

```php
<?php
// Somewhere in your project configuration
$db = new Elegance_Db(new PDO(
    'mysql:host=HOSTNAME;dbname=DATABASE_NAME',
    'USERNAME',
    'PASSWORD'
));
```

# API

## query()

query() is used to execute a sql query and bind parameters to it. 

### Parameters
This method accepts the following arguments:

* $sql - The SQL Query itself
* $bind - An array with parameters to bind to the placeholders in the query (by default you can use "?" as a placeholder)
* $driverOptions - Used to configure placeholders for the $bind array. More information [here](http://www.php.net/manual/en/pdo.prepare.php) 

### Return values
query() returns a PDOStatement object

### Examples

```php
<?php
$db->query('UPDATE `artists` SET `theBest` = 1 WHERE `name` = ?', array('Maceo Parker'));
```
This query is automatically prepared and executed

Example with more parameters binded:

```php
<?php
$sql = 'INSERT INTO `records` (`name`, `author`, `year`) VALUES (?, ?, ?)';
$db->query($sql, array('Kind of blue', 'Miles Davis', 1959));
```

Example with using named parameters:
```php
<?php
$sql = 'DELETE FROM `good_hip_hop_artists` WHERE `name` = :name';
$db->query($sql, array(':name' => 'Lil Wayne'), array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
```
## fetchRow()

fetchRow() is used to retrive a single row from the database

### Parameters

* $sql - The SQL Query itself
* $fetchStyle - Used to set as what (array,object) the row will be returned. You can choose from the following styles: PDO::FETCH_ASSOC, PDO::FETCH_BOTH, PDO::FETCH_BOUND, PDO::FETCH_CLASS, PDO::FETCH_INTO, PDO::FETCH_LAZY, PDO::FETCH_NUM, PDO::FETCH_OBJ. Detailed information about each could be found [here](http://www.php.net/manual/en/pdostatement.fetch.php)
* $bind - An array with parameters to bind to the placeholders in the query (by default you can use "?" as a placeholder)
* $driverOptions - Used to configure placeholders for the $bind array. More information [here](http://www.php.net/manual/en/pdo.prepare.php) 

### Return values
Depends on the $fetchStyle which is used. By default PDO::FETCH_OBJ is used. Look at the examples bellow for more information.

### Examples

```php
<?php
$row = $db->fetchRow('SELECT * FROM `rappers` WHERE `wack` = 1');
echo 'Name: ' . $row->name;
```
Output:
```
Name: Drake
```

We can bind parameters here also:
```php
<?php
$row = $db->fetchRow('SELECT * FROM `rappers` WHERE `name` = ?', array('Big Daddy Kane'));
echo 'Description: ' . $row->description;
```
Output:
```
Description: Very valuable music with good lyrics
```
Let's use different $fetchStyle. The following example demonstrates the usage of PDO::FETCH_ASSOC(fetches as an array where columnName = rowValue):
```php
<?php
$sql = 'SELECT * FROM `samples` WHERE `track` = ? AND `artist` = ?';
$row = $db->fetchRow($sql, array('Suicidal Thoughts', 'The Notorious B.I.G'), PDO::FETCH_ASSOC);
echo 'Sample used: ' . $row['sampledTrack'];
```
Output:
```
Sample used: Miles Davis - Lonley fire
```

## fetchAll()

fetchAll() is almost the same as fetchRow() but returns an array of rows(here the $fetchStyle option refers to every single row in the array, fetchAll() always returns an array).

### Parameters

* $sql - The SQL Query itself
* $fetchStyle - Used to set as what (array,object) the rowa in the rowset will be fetched. You can choose from the following styles: PDO::FETCH_ASSOC, PDO::FETCH_BOTH, PDO::FETCH_BOUND, PDO::FETCH_CLASS, PDO::FETCH_INTO, PDO::FETCH_LAZY, PDO::FETCH_NUM, PDO::FETCH_OBJ. Detailed information about each could be found [here](http://www.php.net/manual/en/pdostatement.fetch.php)
* $bind - An array with parameters to bind to the placeholders in the query (by default you can use "?" as a placeholder)
* $driverOptions - Used to configure placeholders for the $bind array. More information [here](http://www.php.net/manual/en/pdo.prepare.php) 

### Return values
Always return an array with rows, fetched using the given $fetchStyle (uses PDO::FETCH_OBJ by default)

### Examples

```php
<?php
$rows = $db->fetchAll('SELECT * FROM `my_fav_albums` WHERE `year` < ?', array(1990));
var_dump($rows);
```
Output:
```
array(4) {
  [0]=>
  object(stdClass)#4 (4) {
    ["id"]=>
    string(1) "1"
    ["name"]=>
    string(18) "Long Live The Kane"
    ["year"]=>
    string(4) "1988"
    ["author"]=>
    string(14) "Big Daddy Kane"
  }
  [1]=>
  object(stdClass)#5 (4) {
    ["id"]=>
    string(1) "2"
    ["name"]=>
    string(44) "Freedom of Speech... Just Watch What You Say"
    ["year"]=>
    string(4) "1989"
    ["author"]=>
    string(5) "Ice-T"
  }
  [2]=>
  object(stdClass)#6 (4) {
    ["id"]=>
    string(1) "3"
    ["name"]=>
    string(20) "Tougher Than Leather"
    ["year"]=>
    string(4) "1988"
    ["author"]=>
    string(7) "Run DMC"
  }
  [3]=>
  object(stdClass)#7 (4) {
    ["id"]=>
    string(1) "4"
    ["name"]=>
    string(19) "Beneath The Remains"
    ["year"]=>
    string(4) "1989"
    ["author"]=>
    string(9) "Sepultura"
  }
}
```

Example with foreach:
```php
<?php
$rows = $db->fetchAll('SELECT * FROM `my_fav_albums` WHERE `year` < ?', array(1990));

foreach ($rows as $row) {
    echo 'Album: ' . $row->name . '; Year: ' . $row->year . '; By: ' . $row->author . PHP_EOL;
}
```
Output:
```
Album: Long Live The Kane; Year: 1988; By: Big Daddy Kane
Album: Freedom of Speech... Just Watch What You Say; Year: 1989; By: Ice-T
Album: Tougher Than Leather; Year: 1988; By: Run DMC
Album: Beneath The Remains; Year: 1989; By: Sepultura
```

## fetchOne()

fetchOne() is used to return the value of the first column of the first row from the result set.

### Parameters

* $sql - The SQL Query itself
* $bind - An array with parameters to bind to the placeholders in the query (by default you can use "?" as a placeholder)
* $driverOptions - Used to configure placeholders for the $bind array. More information [here](http://www.php.net/manual/en/pdo.prepare.php)

### Return values

Returns the value as a string of the first column of the first row from the result set

### Examples

```php
<?php
$name = $db->fetchOne('SELECT author FROM `my_fav_albums` WHERE `id` = ?', array(4));
echo 'My favorite thrash band is ' . $name;
```
Output:
```
My favorite thrash band is Sepultura
```

## fetchPair()

fetchPair() is used to fetch a select query as an array of key-value pairs. For array keys are used the values of the first column in the select list, and for a values the value of the second column. 

### Parameters 

* $sql - The SQL Query itself
* $bind - An array with parameters to bind to the placeholders in the query (by default you can use "?" as a placeholder)
* $driverOptions - Used to configure placeholders for the $bind array. More information [here](http://www.php.net/manual/en/pdo.prepare.php)

### Return values

Always returns array. If the results set from the query is empty, the returned array is also empty.

### Examples

```php
<?php
$pairs = $db->fetchPair('SELECT `id`, `name` FROM `my_fav_albums`');
var_dump($pairs);
```
Output:
```
array(4) {
  [1]=>
  string(18) "Long Live The Kane"
  [2]=>
  string(44) "Freedom of Speech... Just Watch What You Say"
  [3]=>
  string(20) "Tougher Than Leather"
  [4]=>
  string(19) "Beneath The Remains"
}
```

Using other column as a key:
```php
<?php
$pairs = $db->fetchPair('SELECT `author`, `name` FROM `my_fav_albums`');
var_dump($pairs);
```
Output:
```
array(4) {
  ["Big Daddy Kane"]=>
  string(18) "Long Live The Kane"
  ["Ice-T"]=>
  string(44) "Freedom of Speech... Just Watch What You Say"
  ["Run DMC"]=>
  string(20) "Tougher Than Leather"
  ["Sepultura"]=>
  string(19) "Beneath The Remains"
}
```

## insert()
insert() is used to quickly add row to the database, but you don't have to write any SQL. 

### Parameters

* $table - Table to use (table name)
* $bind - Array of data to insert
* $isIgnore - If true - creates INSERT IGNORE query (false by default)

### Return values

Returns last inserted primary key value

### Examples

```php
<?php
$data = array(
    'name' => 'All we got is uz',
    'author' => 'Onyx',
    'year' => 1995
);
$id = $db->insert('records', $data);
```

Using the $isIgnore flag:

```php
<?php
// Let's assume that there is a UNIQUE index on name and author
$data = array(
    'name' => 'Dead Serious',
    'author' => 'Das EFX',
    'year' => 1992
);
$db->insert('records', $data);
// If we try to run the same again, it won't work, 
// but any errors from duplicate key will be ignored
$db->insert('records', $data, true);
```

## update()

update() is very similar to insert(), but (as you probably guessed) is used for updating row(s).

### Parameters

* $table - Table to use (table name)
* $bind - Array of data to insert
* $where - A typical WHERE condition as a string. An array could be passed for binding parameters. Look at the examples for more information. 
* $limit - Limit the updated rows. If 0 - not limit is applied

### Return values
Returns the count of the affected rows

### Examples

```php
<?php
$data = array(
    'status' => 'destroyed',
    'intendedFor' => 'Inteligent people'
);
$db->update('music', $data, '`genre` = \'Real hip hop\'');
```

Using where as an array of column-value pairs and a limit update up to 3 rows
```php
<?php
$data = array(
    'status' => 'destroyed',
    'intendedFor' => 'People you don\'t wanna mess with'
);
$db->update('music', $data, array('`genre` = ?' => 'Hardcore rap'), 3);
```

## delete()
delete() is very similar to update(), but (as as you probably guessed again) is used for row(s) removal.

### Parameters

* $table - Table to use (table name)
* $where - A typical WHERE condition as a string. An array could be passed for binding parameters. Look at the examples for more information. 
* $limit - Limit the updated rows. If 0 - not limit is applied

### Return values
Returns the count of the affected rows

### Examples 

```php
<?php
$db->delete('music', '`genre` = \'dubstep\'');
```
Using column-value pairs for the WHERE clause and limit up to 100 rows
```php
<?php
$db->delete('music', array('`genre` = ?' => 'hiphop', '`topic` = ?' => 'Money, b*tches, cars'), 100);
```

Ok... at this point I'm probably on some party, getting drunk and sh*t, so... to be continued...