# Trial Day

## Introduction

This is a trial day application using the Zend Framework 3 MVC layer and module
systems.

## Installation

Clone the repository and chmod the following directory

chmod -R g+w,o+w data/cache

trialday.sql file is the database which needs to be imported to mysql database for the application to work.

A file with the following content needs to be created in /config/autoload/local.php

```
<?php
return [
    'db' => [
        'username' => 'database username',
        'password' => 'database password',
    ],
];
```


and database username and password of the mysql database user needs to be placed in this file accordingly.