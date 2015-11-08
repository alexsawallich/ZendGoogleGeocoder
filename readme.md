# What is ZendGoogleGeocoder?
ZendGoogleGeocoder is a *Zend Framework 2*-Module which provides a service to query the [Google Geocoding API](https://developers.google.com/maps/documentation/geocoding/intro).
The module allows you to retrieve the service from your *ServiceLocator* and use a method to retrieve the geocoordinates for the
provided address/location.

The module ships with an API-Client which uses either `file_get_contents` or `cURL` to request data from Google's API.

Each step in the workflow of querying the API is documented by using `Zend\Log`. However by default logging is disabled by using
the [Zend\Log\Writer\Noop](http://framework.zend.com/manual/current/en/modules/zend.log.writers.html#stubbing-out-the-writer)-Writer,
which you can change by overriding a certrain config key (see [Wiki](https://github.com/alexsawallich/ZendGoogleGeocoder/wiki) for more details).

To avoid running into quota-limits the responses from Google's API are being cached [INCOMPLETE].

## Usage Example
*More examples can be found in the [Wiki](https://github.com/alexsawallich/ZendGoogleGeocoder/wiki).*

```php
// For example in an action-method
$address = $this->getRequest()->getPost('address');
$geocoderService = $this->getServiceLocator()->get('ZendGoogleGeocoderService');
$response = $geocoderService->geocodeAddress($address, $format = 'xml');

// Do something with the response here...
```

# Installation

## Step 1: Getting the files
As usual you have the typical three options to install the module.

**Step 1a: Composer**
The preferred way is to use composer. The modules' name is `alexsawallich/zend-google-geocoder`. So you can either put it in the `require`-section of your
composer.json like this:

	"require": {
		// ... more here
		"alexsawallich/zend-google-geocoder": "dev-master",
		// ... more here
	}

or you can add it in commandline like so:

	$ php composer.phar require "alexsawallich/zend-google-geocoder"

When you're done with that run the composer update command:

	$ php composer.phar update

**Step 1b: Git clone**

In commandline head to your `vendor`-directory of your application and run the `git clone` command:

	$ cd /path/to/my/project/vendor
	$ git clone https://github.com/alexsawallich/ZendGoogleGeocoder.git
%
If you have an UI-based client (like GitHub for Windows) you will probably know which buttons you have to click.

**Step 1c: Manual download**

Even though it's a bit old-fashioned and you won't be able to update the module through composer you can of course manually download a ZIP-file from this GitHub-page
and put the extracted folder into your `vendor` or `modules`-directory.

## Step 2: Configuration

After you've got the files you need to enable the module in your `application.config.php`-file.

```php
// application.config.php
return array(
	'modules' => array(
		'ZendGoogleGeocoder'
// ...
```

Now head into the ZendGoogleGeocoder-directory and copy `config/geocoder.global.php.dist` to `root-of-your-project/config/geocoder.global.php`. Open the file
and configure the available options to your needs.

Now you should be able to use the module.

# Notice concerning Google's TOS
If you read the docs of the Google Geocoding API you will find the following ([Source](https://developers.google.com/maps/documentation/geocoding/usage-limits#terms-of-use-restrictions)):
> The Google Maps Geocoding API may only be used in conjunction with a Google map; geocoding results without displaying them on a map is prohibited. For complete details on allowed usage, consult the Maps API Terms of Service License Restrictions.

Please keep that in mind!