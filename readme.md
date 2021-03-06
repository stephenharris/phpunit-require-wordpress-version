# @requires WordPress

This package provides a trait (to use in your test cases) so that you can run your
phpunit tests for specific WordPress versions:

```php
class My_Test_Case extends WP_UnitTestCase {
	use StephenHarris\PHPUnit\RequiresWordPressVersion;

}
```

Then in your tests:

```php
class My_Test extends My_Test_Case {

	/**
	 * @requires WordPress 4.4.0
	 */
	 function testSomethingThatRequiresTermMeta() {
		 	// test will be skipped unless WordPress >= 4.4.0
	 }

}-
```


## Getting Started

### Installation

To install you need:

- PHP 5.4+
- Composer

You can install by running following command in your project folder:

```
composer require stephenharris/phpunit-require-wordpress-version:1.* --dev
```

Alternativevely you can directly edit your composer.json by adding:

```
{
  "require-dev": {
    "stephenharris/phpunit-require-wordpress-version": "~1.0"
  }
}
```

### Setting up your test cases

To use, simply add the `use` statement for the provided trait to your test case:

```php
class My_Test_Case extends WP_UnitTestCase {
	use StephenHarris\PHPUnit\RequiresWordPressVersion;

}
```

This trait overloads the `PHPUnit_Framework_TestCase::checkRequirements()`, if you
already overloading `checkRequirements()` in your test case class then you can alias
the method:

```php
class My_Test_Case extends WP_UnitTestCase {
	use StephenHarris\PHPUnit\RequiresWordPressVersion {
		checkRequirements as checkWordPressVersionRequirements;
	}

	function checkRequirements() {
		$this->checkWordPressVersionRequirements();

		//... your checkRequirements() method here
	}

}
```

If you are using multiple traits with the `checkRequirements()` method, then you will
need to resolve the conflicts using aliases:

```php
class My_Test_Case extends WP_UnitTestCase {
	use StephenHarris\PHPUnit\RequiresWordPressVersion {
		checkRequirements as checkWordPressVersionRequirements;
	}
	use Some\Other\checkRequirementsTrait {
		checkRequirements as checkSomeOtherRequirements;
	}

	function checkRequirements() {
		$this->checkWordPressVersionRequirements();
		$this->checkSomeOtherRequirements();
	}

}
```

## Examples

```php
class My_Test extends My_Test_Case {

	/**
	 * @requires WordPress 4.4.0
	 */
	 function testSomethingRequiresAtLeast440() {
		 	// test will be skipped unless WordPress >= 4.4.0
	 }

	/**
	 * @requires WordPress >= 4.4.0-alpha-123
	 */
	 function testSomethingRequiresAtLeast440alpha123() {
		 	// test will be skipped unless WordPress >= 4.4.0-alpha-123
		 	// also works if you specify 4.4-alpha-123 instead
	 }

	/**
	 * @requires WordPress > 4.6.2-rc-1
	 */
	 function testSomethingRequiresGreaterThan462ReleaseCandidate() {
	 	 // test will be skipped unless WordPress version is greater than 4.6.2-rc-1
	 }

	/**
	 * @requires WordPress == 4.6.0
	 */
	 function testOnlyRunsForWordPress460() {
	 	 // test will only run with version 4.6.0
	 }

	/**
	 * @requires WordPress != 4.6
	 */
	function testSkippedIf460() {
		// test will be skipped if WordPress version is 4.6.0
	}

	/**
	 * @requires WordPress < 4.2-alpha-1234
	 */
	function testOnlyRunsForVersionsBefore42Alpha1234() {
		// test will only run for WordPress versions strictly less than4.2-alpha-1234
	}

	/**
	 * @requires WordPress <= 4.2.0
	 */
	function testOnlyRunsFor420AndEarlier() {
		// test will only run for WordPress version 4.2.0 and earlier
	}

}
```

### Alternative syntax

You use `@requires WordPress` with just a version number, and no operator:

```php
/**
 * @requires WordPress 4.2.0
 */
```

which means the test requires WordPress 4.2.0 or higher to run, and is otherwise
skipped. Or you can specify an operator. E.g:

```php
/**
 * @requires WordPress < 4.2.0
 */
```

which means the test requires a WordPress version strictly before 4.2.0.

The various operators supported are listed below. Also note, that you do not
need to include the patch version number if it is 0.

* WordPress greater than or equal to 4.4.0
  - WordPress 4.4
  - WordPress 4.4.0
  - WordPress >= 4.4.0
  - WordPress ge 4.4.0

* WordPress greater than 4.4.0
  - WordPress > 4.4
  - WordPress > 4.4.0
  - WordPress gt 4.4.0

* WordPress equal to 4.4.0
  - WordPress == 4.4
  - WordPress == 4.4.0
  - WordPress = 4.4.0
  - WordPress eq 4.4.0

* WordPress not equal to 4.4.0
  - WordPress != 4.4
  - WordPress != 4.4.0
  - WordPress ne 4.4.0
  - WordPress <> 4.4.0

* WordPress less than 4.4.0
  - WordPress < 4.4
  - WordPress < 4.4.0
  - WordPress lt 4.4.0

* WordPress less than or equal to 4.4.0
  - WordPress <= 4.4
  - WordPress <= 4.4.0
  - WordPress le 4.4.0


## License

This package is open source and released under MIT license. See LICENSE file for more info.

## Questions? Problems?

Please open an issue at <https://github.com/stephenharris/phpunit-require-wordpress-version/issues>
