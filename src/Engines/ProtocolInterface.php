<?php


namespace WeatherForecast\Engines;


interface ProtocolInterface
{
	public function fetch(
		string $url,
		string $method = 'GET',
		array $headers = [],
		array $postData = []
	): string;
}