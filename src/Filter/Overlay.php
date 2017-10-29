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
namespace Arhitector\Transcoder\Filter;

use Arhitector\Transcoder\Format\FormatInterface;
use Arhitector\Transcoder\TranscodeInterface;

/**
 * Overlay one video on top of another.
 *
 * @package Arhitector\Transcoder\Filter
 */
class Overlay extends FilterChain
{
	
	protected $x = 0;
	
	protected $y = 0;
	
	/**
	 * Overlay constructor.
	 */
	public function __construct()
	{
	
	}
	
	/**
	 * Apply filter.
	 *
	 * @param TranscodeInterface $media
	 * @param FormatInterface    $format
	 *
	 * @return array
	 */
	public function apply(TranscodeInterface $media, FormatInterface $format)
	{
		$this->filters = new \SplPriorityQueue();
		$this->addFilter(new SimpleFilter([
			'filter:v' => [
				'overlay' => urldecode(http_build_query([
					'x' => $this->getX(),
					'y' => $this->getY(),
				], null, ':'))
			]
		]));
		
		return parent::apply($media, $format);
	}
	
	/**
	 * @return int
	 */
	public function getX()
	{
		return $this->x;
	}
	
	/**
	 * Set the expression for the x coordinates of the overlaid video on the main video.
	 *
	 * @param int $x
	 *
	 * @return Overlay
	 */
	public function setX($x)
	{
		$this->x = $x;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getY()
	{
		return $this->y;
	}
	
	/**
	 * Set the expression for the y coordinates of the overlaid video on the main video.
	 *
	 * @param int $y
	 *
	 * @return Overlay
	 */
	public function setY($y)
	{
		$this->y = $y;
		
		return $this;
	}
	
}
