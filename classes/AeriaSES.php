<?php
// Exit if accessed directly.
if( false === defined('AERIA') ) exit;

class SesClientProxyForPHPMailer {
	private $phpmailer;

	public function __construct($phpmailer) {
		$this->phpmailer = $phpmailer;
	}

	public function Send() {
		// Build the raw email
		$this->phpmailer->preSend();
		try {
			return AeriaSES::getClient()->sendRawEmail([
				'RawMessage' => [
					// Get the builded RAW email (comprensive of headers)
					'Data' => base64_encode($this->phpmailer->getSentMIMEMessage())
				]
			]);
		} catch (Exception $e) {
			// wp_mail() catch only phpmailerException
			throw new phpmailerException($e->getMessage(), $e->getCode());
		}
	}
}

class AeriaSES {
	public static $client = null;
	public static $config = [];

	public static function init($key, $secret, $region) {
		require __DIR__.'/../vendor/aws/aws-autoloader.php';
		static::$config = array(
			'key'    => $key,
			'secret' => $secret,
			'region' => $region
		);
	}

	public static function getClient() {
		if (static::$client == null) {
			static::$client = Aws\Ses\SesClient::factory(static::$config);
		}
		return static::$client;
	}

	public static function enable() {
		add_action('phpmailer_init', function(&$phpmailer) {
			static::getClient();
			$phpmailer = new SesClientProxyForPHPMailer($phpmailer);
		});
	}
}
