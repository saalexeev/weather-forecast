<?php


namespace WeatherForecast\Parsers;


use DOMNode;
use DateTime;
use DOMXPath;
use DOMDocument;
use DOMNodeList;
use WeatherForecast\Engines\ParserInterface;
use WeatherForecast\ParserResults\YandexParserResult;
use WeatherForecast\Exceptions\HtmlParseException;
use WeatherForecast\Engines\ParserResultInterface;

class YandexParser implements ParserInterface
{
	private DOMXPath $xPath;

	/**
	 * @param string $content
	 *
	 * @return ParserResultInterface
	 */
	public function parse(string $content): ParserResultInterface
	{
		$dom = $this->loadHtml($content);

		$this->xPath = new DOMXPath($dom);
		$tomorrowWeatherBlockItem = $this->parseTomorrowWeatherBlockItem();

		$dayTemperatureNode = $this->getTemperatureItemFrom($tomorrowWeatherBlockItem, 'day');
		$nightTemperatureNode = $this->getTemperatureItemFrom($tomorrowWeatherBlockItem, 'night');

		return new YandexParserResult(
			$dayTemperatureNode->nodeValue,
			$nightTemperatureNode->nodeValue
		);
	}

	/**
	 * @return string
	 */
	private function getTomorrowDate(): string
	{
		$tomorrowDate = new DateTime('tomorrow');
		return $tomorrowDate->format('Y-m-d');
	}

	/**
	 * @param string $content
	 *
	 * @return DOMDocument
	 */
	private function loadHtml(string $content): DOMDocument
	{
		$options = LIBXML_NOBLANKS | LIBXML_NOCDATA | LIBXML_HTML_NOIMPLIED | LIBXML_COMPACT;
		$dom = new DOMDocument();
		if (!$dom->loadHTML($content, $options)) {
			throw new HtmlParseException('Cannot parse document!');
		}

		return $dom;
	}

	/**
	 * @return DOMNode
	 */
	private function parseTomorrowWeatherBlockItem(): DOMNode
	{
		$tomorrowDate = $this->getTomorrowDate();
		$expr = "//*[contains(@datetime, '$tomorrowDate')]/..";
		$tomorrowWeatherBlock = $this->xPath->query($expr);

		$this->ensureIfNodeListIsValid($tomorrowWeatherBlock);

		return $this->getItemFrom($tomorrowWeatherBlock);
	}

	/**
	 * @param DOMNode $weatherBlock
	 * @param string  $classSuffix
	 *
	 * @return DOMNode
	 */
	private function getTemperatureItemFrom(DOMNode $weatherBlock, string $classSuffix): DOMNode
	{
		$temperatureNode = $this->xPath->query(
			"div[contains(@class, 'forecast-briefly__temp_$classSuffix')]/span[contains(@class, 'with-unit')]",
			$weatherBlock
		);

		$this->ensureIfNodeListIsValid($temperatureNode);

		return $this->getItemFrom($temperatureNode);
	}

	/**
	 * @param DOMNodeList|false $nodeList
	 */
	private function ensureIfNodeListIsValid($nodeList): void
	{
		if ($nodeList === false) {
			throw new HtmlParseException('Node list is invalid');
		}

		if (!$nodeList->length) {
			throw new HtmlParseException('Node list is empty');
		}
	}

	/**
	 * @param DOMNodeList $list
	 *
	 * @return DOMNode
	 */
	private function getItemFrom(DOMNodeList $list): DOMNode
	{
		$item = $list->item(0);
		if ($item === null) {
			throw new HtmlParseException('Cannot get node from DOMNodeList');
		}

		return $item;
	}
}