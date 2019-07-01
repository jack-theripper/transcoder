<?php
/**
 * This file is part of the arhitector/transcoder-ffmpeg library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 *
 * @license   http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2017-2019 Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 */
namespace Arhitector\Transcoder\Preset;

/**
 * Class Preset.
 *
 * @package Arhitector\Transcoder\Preset
 */
class Preset implements PresetInterface, \ArrayAccess
{
	
	/**
	 * @var array The options container.
	 */
	protected $options;
	
	/**
	 * Preset constructor.
	 *
	 * @param string[] $options
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct(array $options)
	{
		foreach ($options as $key => $value)
		{
			$this->offsetSet($key, $value);
		}
	}
	
	/**
	 * Get the options.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return $this->options;
	}
	
	/**
	 * Whether a offset exists
	 *
	 * @param mixed $offset An offset to check for.
	 *
	 * @return boolean true on success or false on failure.
	 */
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->options);
	}
	
	/**
	 * Offset to retrieve
	 *
	 * @param mixed $offset The offset to retrieve.
	 *
	 * @return mixed Can return all value types.
	 */
	public function offsetGet($offset)
	{
		if ($this->offsetExists($offset))
		{
			return $this->options[$offset];
		}
		
		return null;
	}
	
	/**
	 * Offset to set
	 *
	 * @param mixed $offset The offset to assign the value to.
	 * @param mixed $value  The value to set.
	 *
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function offsetSet($offset, $value)
	{
		if ( ! is_scalar($offset))
		{
			throw new \InvalidArgumentException('Wrong offset value.');
		}
		
		$this->options[$offset] = $value;
		
		return $this;
	}
	
	/**
	 * Offset to unset
	 *
	 * @param mixed $offset The offset to unset.
	 *
	 * @return $this
	 */
	public function offsetUnset($offset)
	{
		if ($this->offsetExists($offset))
		{
			unset($this->options[$offset]);
		}
		
		return $this;
	}
	
}
