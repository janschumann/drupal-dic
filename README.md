Drupal dependency injection container integration
=================================================

[![Build Status](https://travis-ci.org/janschumann/drupal-dic.svg)](https://travis-ci.org/janschumann/drupal-dic)

## Overview

Integrates the Symfony´s Dependency Injection Container with drupal.

## Installation

This project can be checked out with [composer](http://getcomposer.org).

```json
{
  "require": {
    "janschumann/dic": "*"
  }
}
```

## Confguration

Usually no configuration is necessary.

The default dic cache dir will be determined by ```DRUPAL_ROOT . '/' . variable_get('file_public_path', '') . '/dic'```.

This can be customized by setting the ```dic_root_dir``` variable.

**Via shell script:**

```sh
$ drush vset dic_root_dir <path/to/cache/dir>
```

**Via php:**

```php
variable_set('dic_root_dir', '<path/to/cache/dir>');
```

## Usage

Class autoloading is done via composer using the [composer_classloader](https://github.com/janschumann/drupal-composer-classloader) module.

By default a ```settings.xml``` or an environment specific derivate (```settings_<environment>.xml```) is loaded.

### Provided services

This module provides the [symfony event dispacher](http://symfony.com/doc/current/components/event_dispatcher/introduction.html) as a
service available through the container.

An instance of the event dispacher can be retrieved by

```php
$dispatcher = drupal_dic()->get('event_dispatcher');
```

To add a listener to an event, add te following to your ```settings.xml``` file.

```xml
<service id="my_service" class="%my_service.class%">
  <tag name="drupal.event_listener" event="<my_event_name>" method="<method_on_my_service>" />
</service>
```

An event is dispached by:

```php
drupal_dic()->get('event_dispatcher')->dispatch('<my_event_name>', <the event class>);
```

### Register bundles

Your modules may implement ```hook_dic_bundle_info()``` as described in ```dic.api.php``` to register their bundles.

**Example:**

```php
/**
 * Implements @see hook_dic_bundle_info()
 */
function <my_module>_dic_bundle_info() {
  return array('bundles' => array("\\MyNamespace\\MyBundle\\Bundle\\MyBundle\\MyBundle"));
}
```



[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/janschumann/drupal-dic/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

