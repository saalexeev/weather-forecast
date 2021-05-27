<?php

use WeatherForecast\Cache\MemoryCache;
use WeatherForecast\Parsers\YandexParser;
use WeatherForecast\Protocols\HttpProtocol;
use WeatherForecast\WeatherServices\YandexWeatherService;

require_once 'vendor/autoload.php';
$cache = new MemoryCache();
$parser = new YandexParser();
$proto = new HttpProtocol();

$service = new YandexWeatherService($proto, $parser, $cache);

var_dump($service->getMoscowWeather());
var_dump($service->getSaintPetersburgWeather());
