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
namespace Arhitector\Transcoder;

/**
 * Dimension, used for manipulating width and height couples.
 *
 * @package Arhitector\Transcoder
 */
class Dimension
{
	
	/**
	 * @var int
	 */
	protected $width;
	
	/**
	 * @var int
	 */
	protected $height;
	
	/**
	 * Dimension Constructor.
	 *
	 * @param int $width
	 * @param int $height
	 *
	 * @throws \InvalidArgumentException when one of the parameters is invalid
	 */
	public function __construct($width, $height)
	{
		$this->setWidth($width);
		$this->setHeight($height);
	}
	
	/**
	 * Returns width.
	 *
	 * @return int
	 */
	public function getWidth()
	{
		return $this->width;
	}
	
	/**
	 * Returns height.
	 *
	 * @return int
	 */
	public function getHeight()
	{
		return $this->height;
	}
	
	/**
	 * Get ratio.
	 *
	 * @return float
	 */
	public function getRatio()
	{
		return $this->getWidth() / $this->getHeight();
	}
	
	/**
	 * Set the width value.
	 *
	 * @param int $width
	 *
	 * @return Dimension
	 * @throws \InvalidArgumentException
	 */
	protected function setWidth($width)
	{
		if ( ! is_numeric($width) || $width <= 0)
		{
			throw new \InvalidArgumentException('The width value should be positive integer.');
		}
		
		$this->width = (int) $width;
		
		return $this;
	}
	
	/**
	 * Set the height value.
	 *
	 * @param int $height
	 *
	 * @return Dimension
	 * @throws \InvalidArgumentException
	 */
	protected function setHeight($height)
	{
		if ( ! is_numeric($height) || $height <= 0)
		{
			throw new \InvalidArgumentException('The height value should be positive integer.');
		}
		
		$this->height = (int) $height;
		
		return $this;
	}
	
}
