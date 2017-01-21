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

use Arhitector\Jumper\Codec;

/**
 * Class FrameFormat.
 *
 * @package Arhitector\Jumper\Format
 */
class FrameFormat implements FrameFormatInterface
{
	use FormatTrait;
	
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
	 * FrameFormat constructor.
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
			
			$this->setFrameCodec($codec);
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
	public function getFrameCodec()
	{
		return $this->frameCodec;
	}
	
	/**
	 * Sets the video/frame codec, should be in the available ones, otherwise an exception is thrown.
	 *
	 * @param Codec $codec
	 *
	 * @return FrameFormat
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
	 *
	 * @return FrameFormat
	 */
	protected function setAvailableFrameCodecs(array $codecs)
	{
		$this->frameAvailableCodecs = array_map('strval', $codecs);
		
		return $this;
	}
	
}
