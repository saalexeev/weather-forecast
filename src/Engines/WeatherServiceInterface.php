<?php


namespace WeatherForecast\Engines;


interface WeatherServiceInterface
{
	public function getMoscowWeather(): ParserResultInterface;

	public function getSaintPetersburgWeather(): ParserResultInterface;
}