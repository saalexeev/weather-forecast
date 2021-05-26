<?php


namespace WeatherForecast\Engines;

use JsonSerializable;

/**
 * Interface CacheInterface
 *
 * @package WeatherForecast\Engines
 */
interface CacheInterface
{
	/**
	 * Get cache key
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function get(string $key);

	/**
	 * Set cache key
	 * @param string $key
	 * @param mixed  $data
	 * @param int    $ttl
	 *
	 * @return void
	 */
	public function set(string $key, JsonSerializable $data, int $ttl = 0): void;

	/**
	 * Check if key exists
	 * @param string $key
	 *
	 * @return bool
	 */
	public function has(string $key): bool;

	/**
	 * Remove key from cache
	 * @param string $key
	 *
	 * @return bool
	 */
	public function forget(string $key): bool;

	/**
	 * Removes array of keys from cache
	 * @param array $keys
	 *
	 * @return bool
	 */
	public function forgetBulk(array $keys): bool;
}