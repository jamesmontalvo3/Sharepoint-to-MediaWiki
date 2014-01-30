<?php
class Sharepoint_cURL {

	public $ch;
	public $url;
	public $login;
	public $user_agent = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.66 Safari/537.36";
	
	public function __construct ($url, $login) {
	
		$this->ch = curl_init();
		$this->url = $url;
		$this->login = $login;
		
	}
	
	public function exe () {
	
		// Thanks to: http://www.tunnelsup.com/using-the-sharepoint-2013-wiki-api
		curl_setopt($this->ch, CURLOPT_HEADER, 0);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_URL, $this->url);
		curl_setopt($this->ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM); // Required if the sharepoint requires authentication
		curl_setopt($this->ch, CURLOPT_USERPWD, $this->login);   // Required if the sharepoint requires authentication
		curl_setopt($this->ch, CURLOPT_USERAGENT,$this->user_agent);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	
		try {
			$ret = curl_exec($this->ch);
		} catch (Exception $e) {
			die("Curl failed: " . $e->getMessage() );
		}

		return $ret;
	
	}
	
}