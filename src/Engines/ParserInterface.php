<?php

namespace WeatherForecast\Engines;

interface ParserInterface
{
	public function parse(string $content): ParserResultInterface;
}