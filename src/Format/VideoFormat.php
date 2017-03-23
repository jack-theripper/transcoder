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
namespace Arhitector\Transcoder\Format;

use Arhitector\Transcoder\Codec;

/**
 * Class VideoFormat.
 *
 * @package Arhitector\Transcoder\Format
 */
class VideoFormat extends AudioFormat implements VideoFormatInterface
{
	
	/**
	 * @var int Passes value.
	 */
	protected $passes = 1;
	
	/**
	 * @var float Frame rate value.
	 */
	protected $videoFrameRate;
	
	/**
	 * @var int Video bit rate value.
	 */
	protected $videoBitrate;
	
	/**
	 * Format constructor.
	 *
	 * @param Codec|string $audioCodec
	 * @param Codec|string $videoCodec
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($audioCodec = null, $videoCodec = null)
	{
		parent::__construct($audioCodec, $videoCodec);
		
		$this->setVideoBitrate(1000000);
	}
	
	/**
	 * Get the video bitrate value.
	 *
	 * @return int
	 */
	public function getVideoBitrate()
	{
		return $this->videoBitrate;
	}
	
	/**
	 * Set the bitrate value.
	 *
	 * @param int $bitrate
	 *
	 * @return VideoFormat
	 * @throws \InvalidArgumentException
	 */
	public function setVideoBitrate($bitrate)
	{
		if ( ! is_numeric($bitrate) || $bitrate < 0)
		{
			throw new \InvalidArgumentException('The video bit rate value must be a integer type.');
		}
		
		$this->videoBitrate = (int) $bitrate;
		
		return $this;
	}
	
	/**
	 * Sets the number of passes.
	 *
	 * @param int $passes
	 *
	 * @return VideoFormat
	 * @throws \InvalidArgumentException
	 */
	public function setPasses($passes)
	{
		if ( ! is_int($passes) || $passes < 1)
		{
			throw new \InvalidArgumentException('The passes value must be a number greater then zero.');
		}
		
		$this->passes = $passes;
		
		return $this;
	}
	
	/**
	 * Get the frame rate value.
	 *
	 * @return float
	 */
	public function getFrameRate()
	{
		return (float) $this->videoFrameRate;
	}
	
	/**
	 * Set the frame rate value.
	 *
	 * @param float $frameRate
	 *
	 * @return VideoFormat
	 * @throws \InvalidArgumentException
	 */
	public function setFrameRate($frameRate)
	{
		if ( ! is_numeric($frameRate) || $frameRate < 0)
		{
			throw new \InvalidArgumentException('Wrong the frame rate value.');
		}
		
		$this->videoFrameRate = $frameRate;
		
		return $this;
	}
	
	/**
	 * Returns the number of passes.
	 *
	 * @return int
	 */
	public function getPasses()
	{
		return $this->passes;
	}
	
}
