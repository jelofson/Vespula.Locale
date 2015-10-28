# README #

### Status ###

[![Build Status](https://travis-ci.org/jelofson/Vespula.Locale.svg?branch=master)](https://travis-ci.org/jelofson/Vespula.Locale)

### A simple class for localized strings ###

This is loosely based on Paul M. Jones' SolarPHP locale implementation.

1. place your locale files into a folder of your choice using xx_YY.php for the naming convention. For example, en_CA.php or en_US.php
2. all your locale files should be in the same folder.
3. when defining your strings, you can use an array. The first element is the singular version. The second is the plural version.

```
<?php 
/* en_CA.php */
return [
    'TEXT_HOME'=>'Home',
    'TEXT_APPLE'=>['apple', 'apples'] // singlular and plural version of the word
];

/* fr_CA.php */
return [
    'TEXT_HOME'=>'Accueil',
    'TEXT_APPLE'=>['pomme', 'pommes']
];
```

Basic usage


```
<?php
$locale = new \Vespula\Locale\Locale('en_CA');
$locale->load('/path/to/locales');

// show all the strings for each language code
print_r($locale->getStrings());

// Get a single string
echo $locale->gettext('TEXT_HOME');

// Get a pluralized form of the string
echo $locale->gettext('TEXT_APPLE', 4);
// displays 'apples'

// switch langs
$locale->setCode('fr_CA');
echo $locale->gettext('TEXT_APPLE', 4);
// displays 'pommes'
```

### Plural Forms ###

Plural forms are important because you can pass a number of objects to the gettext method and return the correct pluralized version. The plural forms can be different for each language. For example, in French, you typically say 0 pomme and not 0 pommes. In English you would say 0 apples and not 0 apple. 

When creating your locale files, you can specify an array of values. The first element is the singular form, and the second is the plural form.

```
<?php
// from en_CA.php 
return [
    'TEXT_PAGE'=>['page', 'pages']
];
```

Plural forms are defined by the format for zero, 1, and more than one objects. The default looks like this

```
| 0       | 1        | 2+      |
+---------+----------+---------+
| plural  | singular | plural  |
+---------+----------+---------+
```

So, 0 apples, 1 apple, 4 apples.

You can set the plural form for a given language using the setPluralForm() method. See below.

```
<?php

$form = [
    'singular', 'plural', 'plural'
];
$locale->setCode('fr_CA');
$locale->setPluralForm('fr_CA', $form);

echo $locale->gettext('TEXT_APPLE', 0);
// displays pomme

echo $locale->gettext('TEXT_APPLE', 2);
// displays pommes

// 0 pomme, 1 pommes, 2 pommes
```

### Miscellaneous ###

```
<?php

// display the current locale code
echo $locale->getCode();
// 'en_CA'

// echo the current language code (first two letters of locale code)
echo $locale->getLanguageCode();
// 'en'

```

You can load new locale files at any time, but existing locale keys will be overwritten.
