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

use Arhitector\Jumper\TranscoderInterface;

/**
 * Class FrameStream.
 *
 * @package Arhitector\Jumper\Stream
 */
class FrameStream implements FrameStreamInterface
{
	use StreamTrait;
	
	/**
	 * @var int Width value.
	 */
	protected $width;
	
	/**
	 * @var int Height value.
	 */
	protected $height;
	
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
	 * Set the width value.
	 *
	 * @param int $width
	 *
	 * @return FrameStream
	 */
	protected function setWidth($width)
	{
		$this->width = (int) $width;
		
		return $this;
	}
	
	/**
	 * Set the height value.
	 *
	 * @param int $height
	 *
	 * @return FrameStream
	 */
	protected function setHeight($height)
	{
		$this->height = (int) $height;
		
		return $this;
	}
	
}
