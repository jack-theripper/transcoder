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
namespace Arhitector\Jumper\Stream;

use Arhitector\Jumper\Exception\TranscoderException;

/**
 * Class Collection.
 *
 * @package Arhitector\Jumper\Stream
 */
class Collection implements \Iterator
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
	 * @throws \Arhitector\Jumper\Exception\TranscoderException
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
	 * @return void
	 */
	public function next()
	{
		next($this->streams);
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
	 * @return void
	 */
	public function rewind()
	{
		reset($this->streams);
	}
	
}
