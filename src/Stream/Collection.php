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
class Collection implements \Iterator, \Countable
{
	
	/**
	 * @var StreamInterface[]
	 */
	protected $streams = [];
	
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
			
			$this->streams[$stream->getIndex()] = $stream;
		}
	}
	
	/**
	 * Return the current stream.
	 *
	 * @return StreamInterface
	 */
	public function current()
	{
		return current($this->streams);
	}
	
	/**
	 * Move forward to next element.
	 *
	 * @return Collection
	 */
	public function next()
	{
		next($this->streams);
		
		return $this;
	}
	
	/**
	 * Return the key of the current element.
	 *
	 * @return int
	 */
	public function key()
	{
		return key($this->streams);
	}
	
	/**
	 * Checks if current position is valid.
	 *
	 * @return boolean
	 */
	public function valid()
	{
		return key($this->streams) !== null;
	}
	
	/**
	 * Rewind the Iterator to the first element.
	 *
	 * @return Collection
	 */
	public function rewind()
	{
		reset($this->streams);
		
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
	
}
