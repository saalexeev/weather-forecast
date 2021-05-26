<?php


namespace WeatherForecast\Protocols;


use JsonException;
use WeatherForecast\Exceptions\HttpException;
use WeatherForecast\Engines\ProtocolInterface;

final class HttpProtocol implements ProtocolInterface
{

	public const HTTP_GET = 'GET';

	public const HTTP_POST = 'POST';

	/**
	 * @param string $url
	 * @param string $method
	 * @param array  $headers
	 * @param array  $postData
	 *
	 * @return string
	 * @throws JsonException
	 * @throws HttpException
	 */
	public function fetch(
		string $url,
		string $method = self::HTTP_GET,
		array $headers = [],
		array $postData = []
	): string {
		$channel = $this->initCurl($url);
		$this->setHeaders($channel, $headers);

		if ($method === self::HTTP_POST) {
			$this->setPostBody($channel, $postData);
		}

		return $this->send($channel);
	}

	/**
	 * @param string $url
	 *
	 * @return false|resource
	 */
	private function initCurl(string $url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		return $ch;
	}

	/**
	 * @param resource $channel
	 * @param array    $headers
	 */
	private function setHeaders($channel, array $headers): void
	{
		foreach ($headers as $name => $value) {
			curl_setopt($channel, CURLOPT_HEADER, "$name: $value");
		}
	}

	/**
	 * @param       $channel
	 * @param array $postData
	 *
	 * @throws JsonException
	 */
	private function setPostBody($channel, array $postData): void
	{
		curl_setopt($channel, CURLOPT_POST, true);
		curl_setopt($channel, CURLOPT_POSTFIELDS, json_encode($postData, JSON_THROW_ON_ERROR));
	}

	/**
	 * @param $channel
	 *
	 * @return string
	 * @throws HttpException
	 */
	private function send($channel): string
	{
		$result = curl_exec($channel);

		if (curl_errno($channel)) {
			throw new HttpException(curl_error($channel));
		}

		return $result;
	}
}

