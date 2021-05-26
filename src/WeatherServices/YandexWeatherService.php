<?php


namespace WeatherForecast\WeatherServices;

use WeatherForecast\Engines\CacheInterface;
use WeatherForecast\Engines\ParserInterface;
use WeatherForecast\Engines\ProtocolInterface;
use WeatherForecast\Parsers\YandexParserResult;
use WeatherForecast\Engines\ParserResultInterface;
use WeatherForecast\Engines\WeatherServiceInterface;

class YandexWeatherService implements WeatherServiceInterface
{
	private ProtocolInterface $client;

	private ParserInterface $parser;

	private CacheInterface $cache;

	private const CACHE_TTL = 60 * 60 * 3; // 3 hrs

	public function __construct(
		ProtocolInterface $protocol,
		ParserInterface $parser,
		CacheInterface $cache
	) {
		$this->client = $protocol;
		$this->parser = $parser;
		$this->cache = $cache;
	}

	/**
	 * @param string $city
	 *
	 * @return ParserResultInterface
	 */
	public function getTomorrowWeather(string $city): ParserResultInterface
	{
		if ($this->cache->has($city)) {
			$rawData = $this->cache->get($city);

			return new YandexParserResult(
				$rawData['day'],
				$rawData['night']
			);
		}

		$rawResponse = $this->client->fetch("https://yandex.ru/pogoda/$city");

		$parserResult = $this->parser->parse($rawResponse);

		$this->cache->set($city, $parserResult, self::CACHE_TTL);

		return $parserResult;
	}
}