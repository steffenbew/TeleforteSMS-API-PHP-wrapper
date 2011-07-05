TeleforteSMS API PHP wrapper
============================

Send SMS with ease via [TeleForte](http://www.teleforte.com).


## Usage example ##

	<?php
	
	require_once 'TeleforteSMS.class.php';
	
	try
	{
		# init new class with API key and sender
		$api = new TeleforteSMS('MY-API-KEY', 'my name');
	
		# add recipients
		$api->add_recipient('431234567890');
		$api->add_recipient('491234567890');
	
		# receive response id
		$message_id = $api->send_message('my message');
	
		echo "The message '$message_id' was sent successfully.";
	}
	catch (Exception $e)
	{
		# catch errors
		echo "An error occured: " . $e->getMessage();
	}
	
	
## Requirements ##

- PHP5
- [cURL Library](http://php.net/manual/en/book.curl.php)


## License (MIT) ##

Copyright (c) 2011 [Steffen Bewersdorff](http://steffenbew.com)

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.