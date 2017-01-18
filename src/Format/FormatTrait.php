<?php
/**
 * This file is part of the arhitector/jumper library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 *
 * @license   http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2017 Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 */
namespace Arhitector\Jumper\Format;

/**
 * Class FormatTrait.
 *
 * @package Arhitector\Jumper\Format
 */
trait FormatTrait
{
	
	/**
	 * @var float Duration value.
	 */
	protected $duration = 0.0;
	
	/**
	 * @var array IDv3 tags or other.
	 */
	protected $tags = [];
	
	/**
	 * @var bool Modified container.
	 */
	protected $modified = false;
	
	/**
	 * @var array List of extensions.
	 */
	protected $extensions = [];
	
	/**
	 * Get the duration value.
	 *
	 * @return float
	 */
	public function getDuration()
	{
		return $this->duration;
	}
	
	/**
	 * Gets the tags.
	 *
	 * @return array
	 */
	public function getTags()
	{
		return $this->tags;
	}
	
	/**
	 * Return the current element
	 *
	 * @return mixed
	 */
	public function current()
	{
		return current($this->tags);
	}
	
	/**
	 * Move forward to next element
	 *
	 * @return void
	 */
	public function next()
	{
		next($this->tags);
	}
	
	/**
	 * Return the key of the current element
	 *
	 * @return int
	 */
	public function key()
	{
		return key($this->tags);
	}
	
	/**
	 * Checks if current position is valid
	 *
	 * @return boolean Returns true on success or false on failure.
	 */
	public function valid()
	{
		return key($this->tags) !== null;
	}
	
	/**
	 * Rewind the Iterator to the first element
	 *
	 * @return void
	 */
	public function rewind()
	{
		reset($this->tags);
	}
	
	/**
	 * Modified container.
	 *
	 * @return bool
	 */
	public function isModified()
	{
		return (bool) $this->modified;
	}
	
	/**
	 * Get the format extensions.
	 *
	 * @return array
	 */
	public function getExtensions()
	{
		return $this->extensions;
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
		return array_key_exists($tagName, $this->tags);
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
			throw new \OutOfBoundsException('Index invalid or out of range.');
		}
		
		return $this->tags[$tagName];
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
		$this->tags[$tagName] = $value;
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
		unset($this->tags[$tagName]);
	}
	
	/**
	 * Set the duration value.
	 *
	 * @param float $duration
	 *
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	protected function setDuration($duration)
	{
		if ( ! is_numeric($duration) || $duration < 0)
		{
			throw new \InvalidArgumentException('Duration value must be a positive number value.');
		}
		
		$this->duration = (float) $duration;
		
		return $this;
	}
	
	/**
	 * Sets the extensions value.
	 *
	 * @param array $extensions
	 *
	 * @return array an array of values that have been set
	 */
	protected function setExtensions(array $extensions)
	{
		return $this->extensions = array_filter($extensions, 'is_scalar');
	}
	
}
