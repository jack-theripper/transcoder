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
	 * @var Codec The frame codec value.
	 */
	protected $frameCodec;
	
	/**
	 * @var int The width value.
	 */
	protected $width;
	
	/**
	 * @var int The height value.
	 */
	protected $height;
	
	/**
	 * @var string[] The list of available frame codecs.
	 */
	protected $frameAvailableCodecs = [];
	
	/**
	 * @var int Passes value.
	 */
	protected $passes = 2;
	
	/**
	 * @var float Frame rate value.
	 */
	protected $videoFrameRate;
	
	/**
	 * @var int Video bit rate value.
	 */
	protected $videoBitrate;
	
	/**
	 * VideoFormat constructor.
	 *
	 * @param Codec|string $audioCodec
	 * @param Codec|string $videoCodec
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($audioCodec = null, $videoCodec = null)
	{
		if ($audioCodec !== null)
		{
			parent::__construct($audioCodec);
		}
		
		if ($videoCodec !== null)
		{
			if ( ! $videoCodec instanceof Codec)
			{
				$videoCodec = new Codec($videoCodec, '');
			}
			
			$this->setFrameCodec($videoCodec);
		}
		
		$this->setVideoBitrate(1000000);
	}
	
	/**
	 * Get width value.
	 *
	 * @return int
	 */
	public function getWidth()
	{
		return $this->width;
	}
	
	/**
	 * Get height value.
	 *
	 * @return int
	 */
	public function getHeight()
	{
		return $this->height;
	}
	
	/**
	 * Get the video/frame codec.
	 *
	 * @return Codec
	 */
	public function getFrameCodec()
	{
		return $this->frameCodec;
	}
	
	/**
	 * Sets the video/frame codec, should be in the available ones, otherwise an exception is thrown.
	 *
	 * @param Codec $codec
	 *
	 * @return VideoFormat
	 * @throws \InvalidArgumentException
	 */
	public function setFrameCodec(Codec $codec)
	{
		if (class_parents($this) && ! in_array($codec, $this->getAvailableFrameCodecs(), false))
		{
			throw new \InvalidArgumentException(sprintf('Wrong video codec value for %s, available values are %s',
				$codec, implode(', ', $this->getAvailableFrameCodecs())));
		}
		
		$this->frameCodec = $codec;
		
		return $this;
	}
	
	/**
	 * Get available codecs.
	 *
	 * @return string[]
	 */
	public function getAvailableFrameCodecs()
	{
		return $this->frameAvailableCodecs;
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
	 * Set the width value.
	 *
	 * @param int $width
	 *
	 * @return VideoFormat
	 * @throws \InvalidArgumentException
	 */
	protected function setWidth($width)
	{
		if ( ! is_numeric($width) || $width < 1)
		{
			throw new \InvalidArgumentException('Wrong the width value.');
		}
		
		$this->width = $width;
		
		return $this;
	}
	
	/**
	 * Set the height value.
	 *
	 * @param int $height
	 *
	 * @return VideoFormat
	 * @throws \InvalidArgumentException
	 */
	protected function setHeight($height)
	{
		if ( ! is_numeric($height) || $height < 1)
		{
			throw new \InvalidArgumentException('Wrong the height value.');
		}
		
		$this->height = $height;
		
		return $this;
	}
	
	/**
	 * Sets the list of available audio codecs.
	 *
	 * @param array $codecs
	 *
	 * @return VideoFormat
	 */
	protected function setAvailableFrameCodecs(array $codecs)
	{
		$this->frameAvailableCodecs = array_map('strval', $codecs);
		
		return $this;
	}
	
}
