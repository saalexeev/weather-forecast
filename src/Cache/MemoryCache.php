<?php


namespace WeatherForecast\Cache;


use JsonException;
use JsonSerializable;
use WeatherForecast\Engines\CacheInterface;

class MemoryCache implements CacheInterface
{
	private array $storage = [];

	/**
	 * @inheritDoc
	 */
	public function get(string $key)
	{
		return $this->storage[$key] ?? null;
	}

	/**
	 * @inheritDoc
	 * @throws JsonException
	 */
	public function set(string $key, JsonSerializable $data, int $ttl = 0): void
	{
		$this->storage[$key] = [
			'value' => json_encode($data, JSON_THROW_ON_ERROR),
			'expires' => $ttl
		];
	}

	/**
	 * @inheritDoc
	 */
	public function has(string $key): bool
	{
		if (!($item = $this->storage[$key] ?? null)) {
			return false;
		}

		if ($this->isExpired($item)) {
			unset($this->storage[$key]);
			return false;
		}

		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function forget(string $key): bool
	{
		unset($this->storage[$key]);
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function forgetBulk(array $keys): bool
	{
		foreach ($keys as $key) {
			$this->forget($key);
		}

		return true;
	}

	private function isExpired(?array $item): bool
	{
		$expires = $item['expires'] ?? 0;
		return $expires !== 0 && time() > $expires;
	}
}