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
namespace Arhitector\Transcoder;

/**
 * Class Point.
 *
 * @package Arhitector\Transcoder
 */
class Point
{
	
	/**
	 * @var int
	 */
	protected $x;
	
	/**
	 * @var int
	 */
	protected $y;
	
	/**
	 * Point constructor.
	 *
	 * @param int $x
	 * @param int $y
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($x, $y)
	{
		$this->setX($x);
		$this->setY($y);
	}
	
	/**
	 * Get X value.
	 *
	 * @return int
	 */
	public function getX()
	{
		return $this->x;
	}
	
	/**
	 * Get Y value.
	 *
	 * @return int
	 */
	public function getY()
	{
		return $this->y;
	}
	
	/**
	 * Return the array of coordinates.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return [
			'x' => $this->getX(),
		    'y' => $this->getY()
		];
	}
	
	/**
	 * Set X value.
	 *
	 * @param int $x
	 *
	 * @return Point
	 * @throws \InvalidArgumentException
	 */
	protected function setX($x)
	{
		if ( ! is_numeric($x) || $x < 0)
		{
			throw new \InvalidArgumentException('The value of x must be a positive numeric.');
		}
		
		$this->x = (int) $x;
		
		return $this;
	}
	
	/**
	 * Set Y value.
	 *
	 * @param int $y
	 *
	 * @return Point
	 * @throws \InvalidArgumentException
	 */
	protected function setY($y)
	{
		if ( ! is_numeric($y) || $y < 0)
		{
			throw new \InvalidArgumentException('The value of y must be a positive numeric.');
		}
		
		$this->y = (int) $y;
		
		return $this;
	}
	
}
