<?php
/**
 * Postmark class, as from gist 1124916
 * see https://gist.github.com/1124916
 *
 * use like this:
 *
 *	require("postmark.php");
 *	$postmark = new Postmark("your-api-key","from-email","optional-reply-to-address");
 *
 *  $success = $postmark->to("receiver@example.com")
 *    ->cc('ccreciever@example.com')
 *    ->bcc('bccreciever@example.com')
 *    ->subject("Email Subject")
 *    ->plain_message("This is a plain text message.")
 *    ->send();
 *
 */
namespace li3_postmark\core;

/**
 * This is a simple library for sending emails with Postmark
 * originally created by Matthew Loberg (http://mloberg.com)
 */
class Postmark {

	/**
	 * Holds the API Key for Postmarkapp
	 *
	 * @var string
	 */
	private $api_key;

	/**
	 * Holds the data, to be used for mailing
	 *
	 * @var array
	 */
	private $data = array();

	/**
	 * Constructor method, takes API Key as well as $from and $reply address
	 *
	 * @param string $apikey API Key for Postmarkapp
	 * @param string $from email address to send emails from
	 * @param string $reply email address to use as reply address
	 */
	function __construct($apikey, $from, $reply = '') {
		$this->api_key = $apikey;
		$this->data["From"] = $from;
		$this->data["ReplyTo"] = $reply;
	}

	/**
	 * Sends the email to postmarkapp service
	 *
	 * needs curl.
	 *
	 * @return boolean true on success, false otherwise
	 */
	function send() {
		$headers = array(
			"Accept: application/json",
			"Content-Type: application/json",
			"X-Postmark-Server-Token: {$this->api_key}"
		);
		$data = $this->data;
		$ch = curl_init('http://api.postmarkapp.com/email');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$return = curl_exec($ch);
		$curl_error = curl_error($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		// do some checking to make sure it sent
		if ($http_code !== 200){
			return false;
		} else {
			return true;
		}
	}

	/**
	 * sets to for email
	 *
	 * @param string $to to which this email is going to be send
	 * @return object instance of this object
	 */
	function to($to) {
		$this->data["To"] = $to;
		return $this;
	}

	/**
	 * sets cc for email
	 *
	 * @param string $cc to which this email is going to be send, in copy
	 * @return object instance of this object
	 */
	function cc($cc) {
		$this->data["Cc"] = $cc;
		return $this;
	}

	/**
	 * sets bcc for email
	 *
	 * @param string $bcc to which this email is going to be send blind
	 * @return object instance of this object
	 */
	function bcc($bcc) {
		$this->data["Bcc"] = $bcc;
		return $this;
	}

	/**
	 * sets subject for email
	 *
	 * @param string $subject subject for this email
	 * @return object instance of this object
	 */
	function subject($subject) {
		$this->data["subject"] = $subject;
		return $this;
	}

	/**
	 * sets html body for email
	 *
	 * @param string $body html content of message to be send
	 * @return object instance of this object
	 */
	function html_message($body) {
		$this->data["HtmlBody"] = "<html><body>{$body}</body></html>";
		return $this;
	}

	/**
	 * sets plain body for email
	 *
	 * @param string $msg content of message to be send plaintext
	 * @return object instance of this object
	 */
	function plain_message($msg) {
		$this->data["TextBody"] = $msg;
		return $this;
	}

	/**
	 * tags this email for postmarkapp
	 *
	 * @param string $tag tag that identifies this email within postmarkapp
	 * @return object instance of this object
	 */
	function tag($tag) {
		$this->data["Tag"] = $tag;
		return $this;
	}
}

?>