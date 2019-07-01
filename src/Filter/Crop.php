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
namespace Arhitector\Transcoder\Filter;

use Arhitector\Transcoder\Dimension;
use Arhitector\Transcoder\Format\FormatInterface;
use Arhitector\Transcoder\Point;
use Arhitector\Transcoder\TranscodeInterface;

/**
 * Crop the input frame to given dimensions.
 *
 * @package Arhitector\Transcoder\Filter
 */
class Crop implements FrameFilterInterface
{
	
	/**
	 * @var Point
	 */
	protected $point;
	
	/**
	 * @var Dimension
	 */
	protected $dimension;
	
	/**
	 * @var bool If set to TRUE will force the output display aspect ratio to be the same of the input.
	 */
	protected $keepAspect = false;
	
	/**
	 * Crop constructor.
	 *
	 * @param Point     $start
	 * @param Dimension $dimension
	 */
	public function __construct(Point $start, Dimension $dimension)
	{
		$this->setPoint($start);
		$this->setDimension($dimension);
	}
	
	/**
	 * Returns the point.
	 *
	 * @return Point
	 */
	public function getPoint()
	{
		return $this->point;
	}
	
	/**
	 * Returns the dimension.
	 *
	 * @return \Arhitector\Transcoder\Dimension
	 */
	public function getDimension()
	{
		return $this->dimension;
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
		return [
			'filter:v' => [
				'crop' => http_build_query([
					'out_w' => $this->getDimension()->getWidth(),
					'out_h' => $this->getDimension()->getHeight(),
					'x'     => $this->getPoint()->getX(),
					'y'     => $this->getPoint()->getX()
				], null, ':')
			]
		];
	}
	
	/**
	 * Set the Point instance.
	 *
	 * @param Point $point
	 *
	 * @return Crop
	 */
	protected function setPoint(Point $point)
	{
		$this->point = $point;
		
		return $this;
	}
	
	/**
	 * Set the Dimension instance.
	 *
	 * @param Dimension $dimension
	 *
	 * @return Crop
	 */
	protected function setDimension(Dimension $dimension)
	{
		$this->dimension = $dimension;
		
		return $this;
	}
	
}
