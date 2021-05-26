<?php


namespace WeatherForecast\Engines;


interface WeatherServiceInterface
{
	public function getTomorrowWeather(string $city);
}