# Response

`Response` represents all the things that are sent to users in response to his `Request`.
Sending `Response` to client:

	$response = new \Moss\Http\Response\Response($responseContent);
	$response->send();

This will output HTML response (`Content-Type: text/html`) with `status code` `200` (`OK`) and content equal to `$responseContent`.

## Content type & Status

When creating response, you set (your own or default values) responses `Content-Type` and its `status code`.
Content type defines what response returns to user, if it is plain text, `HTML` or `PDF`.
While `status code` defines what response means, eg: `200` means everything is `OK`, `404` means `Not Found`, `500` server error, and so on.

## Additional headers

To add, change or remove header from response use `::header($header, $value)` method:

	$Response->header()->set('Content-Type', 'text/plain'); // set header
	$Response->header()->set('Content-Type', 'text/html'); // overwrite previous

To remove header

	$Response->removeHeader('Content-Type');

To retrieve header:

	$header = $Response->header()->get('Content-Type');
	$header = $Response->header()->get('Content-Type', 'default-value-when-header-does-not-exist');

There are tree convenient methods to set caching:

 * `::makeNoCache()` setting `Cache-Control` to `no-cache`, same to `Pragma` - this is default value
 * `::makePublic()` setting `Cache-Control` and `Pragma` to `public`
 * `::makePrivate()` same as above but sets `private`

## Redirect

There is different response object - `RedirectResponse`. Its purpose is to redirect user to other URL.

	$Redirect = new \Moss\Http\Response\RedirectResponse('http://google.com');
	$Redirect->send();

`RedirectResponse` extends `Response`, main difference are: `::__construct($address, $delay = 0)` and `::address($address = null)`, `::delay($delay = null)`

If `$delay` is different than `0`, redirect will be made with usage of `JavaScript` after delay equal to `$delay` in seconds.
