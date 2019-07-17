<?php

	namespace Stoic\Web;

	use Stoic\Utilities\ParameterHelper;

	class Request {
		public static function fromGlobals() : Request {
			return new Request();
		}

		// static to get from parameterhelper or arrays, can't decide


		// ctor to go from opposite of above static probably

		/**
		 * Retrieves the message's request target.
		 * Retrieves the message's request-target either as it will appear (for
		 * clients), as it appeared at request (for servers), or as it was
		 * specified for the instance (see withRequestTarget()).
		 * 
		 * In most cases, this will be the origin-form of the composed URI,
		 * unless a value was provided to the concrete implementation (see
		 * withRequestTarget() below).
		 * 
		 * If no URI is available, and no request-target has been specifically
		 * provided, this method MUST return the string "/".
		 *
		 * @return string
		 */
		public function getRequestTarget() {
			// TODO: implement the function Psr\Http\Message\RequestInterface::getRequestTarget
		}

		/**
		 * Retrieves the HTTP method of the request.
		 *
		 * @return string Returns the request method.
		 */
		public function getMethod() {
			// TODO: implement the function Psr\Http\Message\RequestInterface::getMethod
		}

		/**
		 * Retrieves the URI instance.
		 * This method MUST return a UriInterface instance.
		 *
		 * @return \Psr\Http\Message\UriInterface Returns a UriInterface instance
		representing the URI of the request.
		 */
		public function getUri() {
			// TODO: implement the function Psr\Http\Message\RequestInterface::getUri
		}

		/**
		 * Retrieves the HTTP protocol version as a string.
		 * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
		 *
		 * @return string HTTP protocol version.
		 */
		public function getProtocolVersion() {
			// TODO: implement the function Psr\Http\Message\MessageInterface::getProtocolVersion
		}

		/**
		 * Retrieves all message header values.
		 * The keys represent the header name as it will be sent over the wire, and
		 * each value is an array of strings associated with the header.
		 * 
		 * // Represent the headers as a string
		 * foreach ($message->getHeaders() as $name => $values) {
		 * echo $name . ": " . implode(", ", $values);
		 * }
		 * 
		 * // Emit headers iteratively:
		 * foreach ($message->getHeaders() as $name => $values) {
		 * foreach ($values as $value) {
		 * header(sprintf('%s: %s', $name, $value), false);
		 * }
		 * }
		 * 
		 * While header names are not case-sensitive, getHeaders() will preserve the
		 * exact case in which headers were originally specified.
		 *
		 * @return string[][] Returns an associative array of the message's headers. Each
		key MUST be a header name, and each value MUST be an array of strings
		for that header.
		 */
		public function getHeaders() {
			// TODO: implement the function Psr\Http\Message\MessageInterface::getHeaders
		}

		/**
		 * Checks if a header exists by the given case-insensitive name.
		 *
		 * @param string $name Case-insensitive header field name.
		 *
		 * @return bool Returns true if any header names match the given header
		 * name using a case-insensitive string comparison. Returns false if
		 * no matching header name is found in the message.
		 */
		public function hasHeader($name) {
			// TODO: implement the function Psr\Http\Message\MessageInterface::hasHeader
		}

		/**
		 * Retrieves a message header value by the given case-insensitive name.
		 * This method returns an array of all the header values of the given
		 * case-insensitive header name.
		 * 
		 * If the header does not appear in the message, this method MUST return an
		 * empty array.
		 *
		 * @param string $name Case-insensitive header field name.
		 *
		 * @return string[] An array of string values as provided for the given
		header. If the header does not appear in the message, this method MUST
		return an empty array.
		 */
		public function getHeader($name) {
			// TODO: implement the function Psr\Http\Message\MessageInterface::getHeader
		}

		/**
		 * Retrieves a comma-separated string of the values for a single header.
		 * This method returns all of the header values of the given
		 * case-insensitive header name as a string concatenated together using
		 * a comma.
		 * 
		 * NOTE: Not all header values may be appropriately represented using
		 * comma concatenation. For such headers, use getHeader() instead
		 * and supply your own delimiter when concatenating.
		 * 
		 * If the header does not appear in the message, this method MUST return
		 * an empty string.
		 *
		 * @param string $name Case-insensitive header field name.
		 *
		 * @return string A string of values as provided for the given header
		concatenated together using a comma. If the header does not appear in
		the message, this method MUST return an empty string.
		 */
		public function getHeaderLine($name) {
			// TODO: implement the function Psr\Http\Message\MessageInterface::getHeaderLine
		}

		/**
		 * Gets the body of the message.
		 *
		 * @return \Psr\Http\Message\StreamInterface Returns the body as a stream.
		 */
		public function getBody() {
			// TODO: implement the function Psr\Http\Message\MessageInterface::getBody
		}
		
		/**
		 * Retrieve server parameters.
		 * Retrieves data related to the incoming request environment,
		 * typically derived from PHP's $_SERVER superglobal. The data IS NOT
		 * REQUIRED to originate from $_SERVER.
		 *
		 * @return array
		 */
		public function getServerParams() {
			// TODO: implement the function Psr\Http\Message\ServerRequestInterface::getServerParams
		}

		/**
		 * Retrieve cookies.
		 * Retrieves cookies sent by the client to the server.
		 * 
		 * The data MUST be compatible with the structure of the $_COOKIE
		 * superglobal.
		 *
		 * @return array
		 */
		public function getCookieParams() {
			// TODO: implement the function Psr\Http\Message\ServerRequestInterface::getCookieParams
		}

		/**
		 * Retrieve query string arguments.
		 * Retrieves the deserialized query string arguments, if any.
		 * 
		 * Note: the query params might not be in sync with the URI or server
		 * params. If you need to ensure you are only getting the original
		 * values, you may need to parse the query string from `getUri()->getQuery()`
		 * or from the `QUERY_STRING` server param.
		 *
		 * @return array
		 */
		public function getQueryParams() {
			// TODO: implement the function Psr\Http\Message\ServerRequestInterface::getQueryParams
		}

		/**
		 * Retrieve normalized file upload data.
		 * This method returns upload metadata in a normalized tree, with each leaf
		 * an instance of Psr\Http\Message\UploadedFileInterface.
		 * 
		 * These values MAY be prepared from $_FILES or the message body during
		 * instantiation, or MAY be injected via withUploadedFiles().
		 *
		 * @return array An array tree of UploadedFileInterface instances; an empty
		array MUST be returned if no data is present.
		 */
		public function getUploadedFiles() {
			// TODO: implement the function Psr\Http\Message\ServerRequestInterface::getUploadedFiles
		}

		/**
		 * Retrieve any parameters provided in the request body.
		 * If the request Content-Type is either application/x-www-form-urlencoded
		 * or multipart/form-data, and the request method is POST, this method MUST
		 * return the contents of $_POST.
		 * 
		 * Otherwise, this method may return any results of deserializing
		 * the request body content; as parsing returns structured content, the
		 * potential types MUST be arrays or objects only. A null value indicates
		 * the absence of body content.
		 *
		 * @return null|array|object The deserialized body parameters, if any.
		These will typically be an array or object.
		 */
		public function getParsedBody() {
			// TODO: implement the function Psr\Http\Message\ServerRequestInterface::getParsedBody
		}

		/**
		 * Retrieve attributes derived from the request.
		 * The request "attributes" may be used to allow injection of any
		 * parameters derived from the request: e.g., the results of path
		 * match operations; the results of decrypting cookies; the results of
		 * deserializing non-form-encoded message bodies; etc. Attributes
		 * will be application and request specific, and CAN be mutable.
		 *
		 * @return array Attributes derived from the request.
		 */
		public function getAttributes() {
			// TODO: implement the function Psr\Http\Message\ServerRequestInterface::getAttributes
		}

		/**
		 * Retrieve a single derived request attribute.
		 * Retrieves a single derived request attribute as described in
		 * getAttributes(). If the attribute has not been previously set, returns
		 * the default value as provided.
		 * 
		 * This method obviates the need for a hasAttribute() method, as it allows
		 * specifying a default value to return if the attribute is not found.
		 *
		 * @param string $name The attribute name.
		 * @param mixed $default Default value to return if the attribute does not exist.
		 *
		 * @return mixed
		 */
		public function getAttribute($name, $default = null) {
			// TODO: implement the function Psr\Http\Message\ServerRequestInterface::getAttribute
		}
	}
