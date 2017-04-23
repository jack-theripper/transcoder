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
use Arhitector\Transcoder\TimeInterval;

/**
 * Class FrameFormat.
 *
 * @package Arhitector\Transcoder\Format
 */
class FrameFormat implements FrameFormatInterface
{
	use FormatTrait;
	
	/**
	 * @var Codec The video codec value.
	 */
	protected $videoCodec;
	
	/**
	 * @var int The width value.
	 */
	protected $width;
	
	/**
	 * @var int The height value.
	 */
	protected $height;
	
	/**
	 * @var string[] The list of available video codecs.
	 */
	protected $videoAvailableCodecs = [];
	
	/**
	 * Format constructor.
	 *
	 * @param Codec|string $codec
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($codec = null)
	{
		if ($codec !== null)
		{
			if ( ! $codec instanceof Codec)
			{
				$codec = new Codec($codec);
			}
			
			$this->setVideoCodec($codec);
		}
		
		if ( ! $this->getDuration())
		{
			$this->setDuration(new TimeInterval(0));
		}
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
	 * Returns the number of passes.
	 *
	 * @return int
	 */
	public function getPasses()
	{
		return 1;
	}
	
	/**
	 * Get the video/frame codec.
	 *
	 * @return Codec
	 */
	public function getVideoCodec()
	{
		return $this->videoCodec;
	}
	
	/**
	 * Sets the video/frame codec, should be in the available ones, otherwise an exception is thrown.
	 *
	 * @param Codec $codec
	 *
	 * @return FrameFormat
	 * @throws \InvalidArgumentException
	 */
	public function setVideoCodec(Codec $codec)
	{
		if ($this->getAvailableVideoCodecs() && ! in_array($codec, $this->getAvailableVideoCodecs(), false))
		{
			throw new \InvalidArgumentException(sprintf('Wrong video codec value for "%s", available values are %s',
				$codec, implode(', ', $this->getAvailableVideoCodecs())));
		}
		
		$this->videoCodec = $codec;
		
		return $this;
	}
	
	/**
	 * Get available codecs.
	 *
	 * @return string[]
	 */
	public function getAvailableVideoCodecs()
	{
		return $this->videoAvailableCodecs;
	}
	
	/**
	 * Set the width value.
	 *
	 * @param int $width
	 *
	 * @return FrameFormat
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
	 * @return FrameFormat
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
	 * @param bool  $force
	 *
	 * @return \Arhitector\Transcoder\Format\FrameFormat
	 */
	protected function setAvailableVideoCodecs(array $codecs, $force = false)
	{
		if ( ! $force && $this->getAvailableVideoCodecs())
		{
			$codecs = array_intersect($this->getAvailableVideoCodecs(), $codecs);
		}
		
		$this->videoAvailableCodecs = array_map('strval', $codecs);
		
		return $this;
	}
	
}
