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
 * Rotate frame or video by an arbitrary angle expressed in radians.
 *
 * @package Arhitector\Transcoder\Filter
 */
class Rotate implements FrameFilterInterface
{
	
	/**
	 * @var string The angle in radians.
	 */
	protected $angle = 0;
	
	/**
	 * @var bool Enable bilinear interpolation if set to TRUE.
	 */
	protected $bilinear = true;
	
	/**
	 * @var string Color to fill the output area.
	 */
	protected $color = 'black';
	
	/**
	 * Rotate constructor.
	 *
	 * @param int $angle
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($angle = null)
	{
		if ($angle !== null)
		{
			$this->setAngleDegrees($this->angle);
		}
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
		$expressions = [
			'%frame%'      => 'n',
			'%frame_time%' => 't',
			'%width%'      => 'in_w',
			'%height%'     => 'in_h',
			'%width_out%'  => 'out_w',
			'%height_out%' => 'out_h',
		];
		
		return [
			'filter:v' => [
				'rotate' => http_build_query([
					'angle'            => str_replace(array_keys($expressions), $expressions, $this->getAngle()),
					'bilinear'         => $this->isBilinear(),
					'fillcolor'        => $this->getColor()
				], null, ':')
			]
		];
	}
	
	/**
	 * The angle value in radians. A negative value will result in a counter-clockwise rotation.
	 * This expression is evaluated for each frame.
	 *
	 * @param string $radians
	 *
	 * @return Rotate
	 * @throws \InvalidArgumentException
	 */
	public function setAngle($radians)
	{
		if ( ! is_numeric($radians))
		{
			throw new \InvalidArgumentException('Angle radians must be a numeric.');
		}
		
		$this->angle = $radians;
		
		return $this;
	}
	
	/**
	 * The angle value in degrees.
	 *
	 * @param int $degree
	 *
	 * @return Rotate
	 * @throws \InvalidArgumentException
	 */
	public function setAngleDegrees($degree)
	{
		if ( ! is_numeric($degree))
		{
			throw new \InvalidArgumentException('Angle degrees must be a numeric.');
		}
		
		$this->setAngle($degree * pi() / 180);
		
		return $this;
	}
	
	/**
	 * Set an expression for the angle by which to rotate the input video clockwise, expressed as a number of radians.
	 * A negative value will result in a counter-clockwise rotation.
	 *
	 * %frame% - sequential number of the input frame, starting from 0.
	 * %frame_time% - time in seconds of the input frame, it is set to 0 when the filter is configured.
	 * %width% - the input frame width.
	 * %height% - the input frame height.
	 * %width_out% - the output width, that is the size of the padded area as specified by the width expressions.
	 * %height_out% - the output height, that is the size of the padded area as specified by the height expressions.
	 *
	 * @param string $expression
	 *
	 * @return Rotate
	 * @throws \InvalidArgumentException
	 */
	public function setAngleExpression($expression)
	{
		if ( ! is_scalar($expression))
		{
			throw new \InvalidArgumentException('The expression value must be a string type.');
		}
		
		$this->angle = $expression;
		
		return $this;
	}
	
	/**
	 * Get current angle value.
	 *
	 * @return string
	 */
	public function getAngle()
	{
		return $this->angle;
	}
	
	/**
	 * Enable bilinear interpolation or not.
	 *
	 * @return bool
	 */
	public function isBilinear()
	{
		return $this->bilinear;
	}
	
	/**
	 * Enable bilinear interpolation if set to TRUE, a value of FALSE disables it.
	 *
	 * @param bool $bilinear
	 *
	 * @return Rotate
	 */
	public function setBilinear($bilinear)
	{
		$this->bilinear = (bool) $bilinear;
		
		return $this;
	}
	
	/**
	 * Set the color used to fill the output area not covered by the rotated image.
	 *
	 * @param string $color
	 *
	 * @return Rotate
	 *
	 * // TODO: добавить валидацию цвета
	 */
	public function setColor($color)
	{
		$this->color = (string) $color;
		
		return $this;
	}
	
	/**
	 * Get fill color value.
	 *
	 * @return string
	 */
	public function getColor()
	{
		return $this->color;
	}
	
}
