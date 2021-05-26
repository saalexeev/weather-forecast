<?php


namespace WeatherForecast\Parsers;


use WeatherForecast\Engines\ParserResultInterface;

class YandexParserResult implements ParserResultInterface
{
	private float $day;

	private float $night;

	public function __construct(float $day, float $night)
	{
		$this->day = $day;
		$this->night = $night;
	}

	public function day(): float
	{
		return $this->day;
	}

	public function night(): float
	{
		return $this->night;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return [
			'day'   => $this->day(),
			'night' => $this->night(),
		];
	}
}