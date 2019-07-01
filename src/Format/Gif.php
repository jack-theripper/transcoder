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
namespace Arhitector\Transcoder\Format;

use Arhitector\Transcoder\Codec;

/**
 * The Gif picture format.
 *
 * @package Arhitector\Transcoder\Format
 */
class Gif extends FrameFormat
{
	
	/**
	 * @var float Frame delay value.
	 */
	protected $frameDelay = .1;
	
	/**
	 * @var int The loop count value.
	 */
	protected $loopCount = -1;
	
	/**
	 * Format constructor.
	 *
	 * @param Codec|string $codec
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($codec = 'gif')
	{
		parent::__construct($codec);
		
		$this->setExtensions(['gif']);
		$this->setAvailableVideoCodecs(['gif']);
	}
	
	/**
	 * Set frame delay value
	 *
	 * @param int|float $frame_delay
	 *
	 * @return Gif
	 */
	public function setFrameDelay($frame_delay)
	{
		// TODO
	}
	
	/**
	 * Get frame delay value.
	 *
	 * @return float
	 */
	public function getFrameDelay()
	{
		return $this->frameDelay;
	}
	
	/**
	 * Sets loop count value.
	 *
	 * @param int $loop_count
	 *
	 * @return Gif
	 */
	public function setLoopCount($loop_count)
	{
		// TODO
	}
	
	/**
	 * Get the loop count value.
	 *
	 * @return int
	 */
	public function getLoopCount()
	{
		return $this->loopCount;
	}
	
}
