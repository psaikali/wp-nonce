# Use WordPress nonces in an Object-Oriented way

This library gives you the ability to use WordPress Nonces functions in an object-oriented way.
Instantiate a Nonce object and use it to create or verify a nonce.

## Installation

Add private repository and require the library:
```
composer config repositories.repo-name vcs git@bitbucket.org:pskli/wp-nonce.git
composer require pskli/wp-nonce
```

And include your Composer autoloader:
```
require 'vendor/autoload.php';

use Pskli\Nonce\Nonce;
use Pskli\Nonce\Nonce_URL;
use Pskli\Nonce\Nonce_Field;
```

---

## Basic usage

Simply instantiate a new `Nonce`, `Nonce_URL` or `Nonce_Field` and use the `create()` method on it to generate a nonce / a URL with a nonce in it / a nonce in an hidden input field.

Use the `isValid()` method on your object to check the validity of a nonce.

See below for more details.

---

## Creating simple nonce strings
```
$simple_nonce_object = new Nonce();
$simple_nonce = $simple_nonce_object->create();
echo $simple_nonce;
```
Results in: `ccf4432209`

#### Change the nonce action
The first (optional) parameter passed in the constructor is the action used to generate the nonce.

It can be a simple string:
```
$simple_nonce_object = new Nonce( 'delete_stuff' );
```

Or it can be an array to generate a `sprintf()`-like action.
```
$simple_nonce_object = new Nonce( [ 'delete_%s_%d', 'user', 12 ] );
```
The code above will change the nonce action to `delete_user_12`. 
Useful to generate more secure and specific nonces and to avoid writing `sprintf()`!

---
## Creating nonce URLs
```
$url_nonce_object = new Nonce_URL();
$url_nonce = $url_nonce_object->create( 'https://wordpress.org' );
echo $url_nonce;
```
Results in: `https://wordpress.org?_wpnonce=ccf4432209`

You need to pass a valid URL as a parameter to the `create()` method.

#### Changing the key used to store the nonce
The second (optional) parameter passed in the constructor is the key used to store the generated nonce.
```
$url_nonce_object = new Nonce_URL( null, 'custom_key' );
$url_nonce = $url_nonce_object->create( 'https://wordpress.org' );
echo $url_nonce;
```
Results in: `https://wordpress.org?custom_key=ccf4432209`

---
## Creating nonce fields
```
$field_nonce_object = new Nonce_Field();
$field_nonce = $field_nonce_object->create();
echo $field_nonce;
```
Results in: `<input type="hidden" id="_wpnonce" name="_wpnonce" value="ccf4432209" /><input type="hidden" name="_wp_http_referer" value="/" />`

You can pass `false` as a parameter to the `create()` method in order to generate only a nonce hidden input field, without the hidden referer field.

#### Changing the key name used to store the nonce
Just like the `Nonce_URL` class, `Nonce_Field` key can be changed via the second (optional) parameter.
```
echo ( new Nonce_Field( null, 'custom_key' ) )->create( false );
```
Results in `<input type="hidden" id="custom_key" name="custom_key" value="ccf4432209" />`

---
## Validating nonces
You need to check if a nonce is valid when processing an action (a form, a URL...). 
Simply instantiate a nonce object (potentially with the correct `$action` and `$key` parameters) and use the `is_valid( $value )` method on it.
If no `$value` is passed, the library will try to look for the expected key in URL (for `Nonce_URL`) or request (for `Nonce_Field`).

#### Validating a nonce string
```
$is_valid = ( new Nonce( 'your_action' ) )->is_valid( 'ccf4432209' );
```
Results in a boolean.

#### Validating a nonce stored in a URL
You can simply use the previous example and check against the `$_GET` value you'd like to verify, passing it to the `is_valid()` method.
Or you can create a `Nonce_URL` object with the correct (but optional) `$action` and `$key` parameters and use the `is_valid()` method on it to let the library automatically look for the correct `$_GET` parameter in the URL.
```
$nonce_url = new Nonce_URL( 'some_action', 'custom_key' );
var_dump( $nonce_url->is_valid() )
```
Results in `true` if the current URL of the script contains the correct nonce `$_GET` parameter, like `http://example.com/?custom_key=bb5d696880`.

#### Validating a nonce passed by a hidden field
It is quite similar to validating a nonce stored in a URL (see previous section).

You can create a `Nonce_Field` object with the correct (but optional) `$action` and `$key` parameters and use the `is_valid()` method on it to let the library automatically look for the correct `$_REQUEST` parameter in the request.
```
$nonce_field = new Nonce_Field( 'some_action', 'custom_key' );
var_dump( $nonce_field->is_valid() )
```
Results in `true` if the current request of the script contains a correct nonce  passed by a hidden input field via `$_GET`/`$_POST` parameter.

You can still pass a `$value` parameter to `is_valid( $value )` if you already stored the nonce to be checked in a variable.

---
## Admin & AJAX requests

#### Checking an admin request
```
$admin_nonce = new Nonce( $action, $key );
var_dump( $admin_nonce->is_valid_admin_request() );
```
Results in `true` if the current `$_REQUEST[ $key ]` stores a valid nonce for the `$action` action, and that the referer is a valid admin page.

#### Checking an AJAX request
```
$admin_nonce = new Nonce( $action, $key );
var_dump( $admin_nonce->is_valid_ajax_request( false ) );
```
Results in `true` if the current `$_REQUEST[ $key ]` (or `$_REQUEST[ '_ajax_nonce' ]` or `$_REQUEST[ '_wpnonce' ]`) stores a valid nonce for the `$action` action.

---
## Changelog
2018-09-20 :
- first release marking v1.0