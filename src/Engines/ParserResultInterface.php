<?php


namespace WeatherForecast\Engines;


use JsonSerializable;

interface ParserResultInterface extends JsonSerializable
{
	public function day();

	public function night();
}