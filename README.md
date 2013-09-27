couchdb-logging
=====================

Simple logging system for WordPress using CouchDB

## Installation

Download the plugin via GitHub and add this to your `wp-config.php` or something.

```php
<?php

define('CB_HOST', '127.0.0.1');
define('CB_PORT', '5984');
define('CB_NAME', 'my-awesome-database');

// you can also have login, optional.

define('CB_USER', 'user');
define('CB_PASSWORD', 'password');
?>
```

If no CouchDB options is defined it will use the host `127.0.0.1`, the default port `5984` and the database name `wordpress-logging` . Now you are ready to start logging!

```php
<?php

// Will return the document from the database or false if something goes wrong.
couchdb_log('info', array('status' => 'ok'));

?>
```

Get all logs from the database

```php
<?php
  
  $logs = get_couchdb_logs([$limit ,] [$endKey ,] [$descending]);

?>
```

* `$limit` Is the count of documents to return.
* `$endKey` Is the timestamp to stop at.
* `$descending` It's speaks for it self.

## Todo

* Add WordPress view where you can sort and view the logs.
* Fix so it don't break the site when Sag failes.
