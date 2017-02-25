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
namespace Arhitector\Transcoder\Stream;

use Arhitector\Transcoder\Exception\TranscoderException;

/**
 * Class Collection.
 *
 * @package Arhitector\Transcoder\Stream
 */
class Collection implements \Iterator, \Countable, \ArrayAccess
{
	
	/**
	 * @var StreamInterface[]
	 */
	protected $streams = [];
	
	/**
	 * @var int Current position
	 */
	private $position = 0;
	
	/**
	 * Collection constructor.
	 *
	 * @param array $streams
	 *
	 * @throws \Arhitector\Transcoder\Exception\TranscoderException
	 */
	public function __construct(array $streams = [])
	{
		foreach ($streams as $stream)
		{
			if ( ! $stream instanceof StreamInterface)
			{
				throw new TranscoderException('Class instance must be instanceof "StreamInterface".');
			}
		}
		
		$this->streams = $streams;
	}
	
	/**
	 * Returns the first StreamInterface from the list.
	 *
	 * @return StreamInterface
	 */
	public function getFirst()
	{
		return reset($this->streams);
	}
	
	/**
	 * Return the current stream.
	 *
	 * @return StreamInterface
	 */
	public function current()
	{
		return $this->streams[$this->position];
	}
	
	/**
	 * Move forward to next element.
	 *
	 * @return Collection
	 */
	public function next()
	{
		$this->position++;
		
		return $this;
	}
	
	/**
	 * Return the key of the current element.
	 *
	 * @return int
	 */
	public function key()
	{
		return $this->position;
	}
	
	/**
	 * Checks if current position is valid.
	 *
	 * @return boolean
	 */
	public function valid()
	{
		return isset($this->streams[$this->position]);
	}
	
	/**
	 * Rewind the Iterator to the first element.
	 *
	 * @return Collection
	 */
	public function rewind()
	{
		$this->position = 0;
		
		return $this;
	}
	
	/**
	 * Count streams of an collection.
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->streams);
	}
	
	/**
	 * Whether a index exists.
	 *
	 * @param int $index
	 *
	 * @return bool
	 */
	public function offsetExists($index)
	{
		return isset($this->streams[$index]);
	}
	
	/**
	 * Offset to retrieve.
	 *
	 * @param int $index
	 *
	 * @return StreamInterface
	 * @throws \OutOfBoundsException
	 */
	public function offsetGet($index)
	{
		if ( ! $this->offsetExists($index))
		{
			throw new \OutOfBoundsException('Index invalid or out of range.');
		}
		
		return $this->streams[$index];
	}
	
	/**
	 * Sets a new stream instance at a specified index.
	 *
	 * @param int             $index The index being set.
	 * @param StreamInterface $value The new value for the index.
	 *
	 * @return Collection
	 * @throws TranscoderException
	 */
	public function offsetSet($index, $value)
	{
		if ( ! $value instanceof StreamInterface)
		{
			throw new TranscoderException(sprintf('The new value must be an instance of %s', StreamInterface::class));
		}
		
		$this->streams[$index ?: $this->count()] = $value;
		
		return $this;
	}
	
	/**
	 * Offset to unset stream instance.
	 *
	 * @param int $index
	 *
	 * @return Collection
	 */
	public function offsetUnset($index)
	{
		if ($this->offsetExists($index))
		{
			unset($this->streams[$index]);
		}
		
		return $this;
	}
	
}
