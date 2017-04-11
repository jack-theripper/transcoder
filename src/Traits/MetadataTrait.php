<?php
/**
 * This file is part of the arhitector/transcoder library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 *
 * @license   http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2017 Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 */
namespace Arhitector\Transcoder\Traits;

/**
 * Class MetadataTrait.
 *
 * @package Arhitector\Transcoder\Traits
 */
trait MetadataTrait
{
	
	/**
	 * @var array The metadata tags or other.
	 */
	protected $metadata = [];
	
	/**
	 * Gets the metadata.
	 *
	 * @return array
	 */
	public function getMetadata()
	{
		return $this->metadata;
	}
	
	/**
	 * Check the metadata.
	 *
	 * @param mixed $tagName
	 *
	 * @return boolean
	 */
	public function offsetExists($tagName)
	{
		return array_key_exists($tagName, $this->metadata);
	}
	
	/**
	 * Get metadata.
	 *
	 * @param mixed $tagName
	 *
	 * @return mixed
	 * @throws \OutOfBoundsException
	 */
	public function offsetGet($tagName)
	{
		if ( ! $this->offsetExists($tagName))
		{
			return null;
		}
		
		return $this->metadata[$tagName];
	}
	
	/**
	 * Sets the metadata.
	 *
	 * @param string $tagName
	 * @param mixed  $value The value to set.
	 *
	 * @return void
	 */
	public function offsetSet($tagName, $value)
	{
		$this->metadata[$tagName] = $value;
	}
	
	/**
	 * Removes metadata.
	 *
	 * @param mixed $tagName
	 *
	 * @return void
	 */
	public function offsetUnset($tagName)
	{
		unset($this->metadata[$tagName]);
	}
	
	/**
	 * Sets the metadata.
	 *
	 * @param string|array $metadata
	 * @param mixed        $value
	 *
	 * @return $this
	 */
	protected function setMetadata($metadata, $value = null)
	{
		if ( ! is_array($metadata))
		{
			$metadata = [(string) $metadata => $value];
		}
		
		$this->metadata = array_filter($metadata, 'is_scalar');
		
		return $this;
	}
	
}
