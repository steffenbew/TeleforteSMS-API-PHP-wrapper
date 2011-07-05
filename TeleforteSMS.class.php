<?php

/** 
 * Teleforte SMS API PHP wrapper
 * 
 * @author		Steffen Bewersdorff
 * @mail		mail@steffenbew.com
 * @website		http://steffenbew.com
 * @version		0.1
 * @date		09.02.2011
 * @license		MIT License
 *
 * Copyright (c) 2011 Steffen Bewersdorff, http://steffenbew.com
 * 
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 * 
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

class TeleforteSMS
{
	const API_URL = 'https://sms.lcx.at/messages/post_req';
	
	private $recipients = array(), $api_key, $sender;
		
	
	/* 
	 * Initialize class with API key and optional sender (can be changed later with setter function)
	 *
	 * @api_key	string	API key
	 * @sender	string	SMS sender
	 *
	 * @return	void
	 */	
	function __construct($api_key = NULL, $sender = NULL)
	{
		if($api_key === NULL)
		{
			throw new Exception("You must provide an API key.");
		}
		
		$this->api_key = $api_key;
		
		if($sender !== NULL)
		{
			$this->set_sender($sender);
		}
	}
	
	
	/**
	 * Add a recipient to the list
	 *
	 * @number	string	telephone number (should be formatted as 491234567890)
	 * @return	void
	 */	
	public function add_recipient($number = NULL)
	{
		# validate numeric
		if(preg_match("/^[0-9]+$/", $number) !== 1)
		{
			throw new Exception("Recipient is not a number: $number");
		}
		
		array_push($this->recipients, $number);
	}
	
	
	/** 
	 * Get an array of all recipients
	 *
	 * @return	array	recipients
	 */
	public function get_recipients()
	{
		return $this->recipients;
	}
	
	
	/**
	 * Set sender of SMS (can be alphanumeric)
	 *
	 * @sender	string	alphanumeric sender
	 *
	 * @return	void
	 */	
	public function set_sender($sender = NULL)
	{
		# validate alphanumeric
		if(preg_match("/^[0-9a-zA-Z\s]{1,11}$/", $sender) !== 1)
		{
			throw new Exception("Sender must be alphanumeric and cannot be longer than 11 characters.");
		}
		
		$this->sender = $sender;
	}
	
	
	/**
	 * Send message with given text
	 * (check if all required options have been set)
	 *
	 * @msg		string	text message
	 *
	 * @return	int		response id for further tracking
	 */	
	public function send_message($msg = NULL)
	{
		# check for all required options
		if($this->sender === NULL)
		{
			throw new Exception("You must provide a sender.");
		}
		if(count($this->recipients) === 0)
		{
			throw new Exception("You must provide at least one recipient.");
		}
		if($msg === NULL) {
			throw new Exception("You must provide a message.");
		}
		if(strlen($msg) > 160)
		{
			throw new Exception("The message must not be longer than 160 characters.");
		}
		
		# set post values, expected by api
		$post_values = array(
			'recipient'	=> implode(';', $this->recipients),
			'sender'	=> $this->sender,
			'message'	=> $msg,
			'api_key'	=> $this->api_key
		);
		
		return $this->send_request($post_values);
	}
	
	
	/**
	 * Send API request with all supplied data
	 *
	 * @post_values	array	message data
	 *
	 * @return		int		response id
	 */	
	private function send_request($post_values = array())
	{
		# set cURL options
		$options = array(
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_HEADER			=> false,
			CURLOPT_USERAGENT		=> "TeleforteSMS-API-PHP-wrapper",
			CURLOPT_POST			=> true,
			CURLOPT_POSTFIELDS		=> $post_values,
			CURLOPT_SSL_VERIFYPEER	=> FALSE
		);

		# send request
		$ch			= curl_init(self::API_URL);
					  curl_setopt_array($ch, $options);
		$response	= curl_exec($ch);
		$err		= curl_errno($ch);
		$errmsg		= curl_error($ch);
		$httpCode	= curl_getinfo($ch, CURLINFO_HTTP_CODE);
					  curl_close($ch);
		
		# handle heavy errors
		if($err !== 0)
		{
			# Error at receiving file
			throw new Exception("Could not fetch file: $errmsg");
		}
		if($httpCode !== 200)
		{
			# Wrong HTTP response
			throw new Exception("Request failed. Status code $httpCode returned.");
		}

		# parse response and return
		$valid_response = preg_match("/^([0-9]+):([0-9]+)$/", $response, $response_matches);
		if($valid_response !== 1)
		{
			throw new Exception("Unknown response: $response");
		}
		
		# fetch response values
		$response_code	= (int) $response_matches[1];
		$response_id	= (int) $response_matches[2];
		
		if($response_code !== 200)
		{
			switch($response_code)
			{
				case 0:
					throw new Exception("Host failed to send message. Fatal Error. Please contact support.");
				break;
				case 1:
					throw new Exception("Host failed to send message. Message length exceeds max length of 160 chars.");				
				break;
				default:
					throw new Exception("Host failed to send message. Response code $response_code. Response ID $response_id.");
			}
		}

		return $response_id;
	}
	
}