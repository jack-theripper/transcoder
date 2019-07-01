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
			$this->setAngleDegrees($angle);
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
					'angle'     => str_replace(array_keys($expressions), $expressions, $this->getAngle()),
					'bilinear'  => $this->isBilinear(),
					'fillcolor' => $this->getColor(),
				], null, ':'),
			],
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
	 */
	public function setColor($color)
	{
		if ( ! $this->isValidColor($color))
		{
			throw new \InvalidArgumentException('Invalid color format.');
		}
		
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
	
	/**
	 * Returns a string representing the name of the color.
	 *
	 * @return array
	 */
	public function getColors()
	{
		return [
			'AliceBlue',
			'AntiqueWhite',
			'Aqua',
			'Aquamarine',
			'Azure',
			'Beige',
			'Bisque',
			'Black',
			'BlanchedAlmond',
			'Blue',
			'BlueViolet',
			'Brown',
			'BurlyWood',
			'CadetBlue',
			'Chartreuse',
			'Chocolate',
			'Coral',
			'CornflowerBlue',
			'Cornsilk',
			'Crimson',
			'Cyan',
			'DarkBlue',
			'DarkCyan',
			'DarkGoldenRod',
			'DarkGray',
			'DarkGreen',
			'DarkKhaki',
			'DarkMagenta',
			'DarkOliveGreen',
			'Darkorange',
			'DarkOrchid',
			'DarkRed',
			'DarkSalmon',
			'DarkSeaGreen',
			'DarkSlateBlue',
			'DarkSlateGray',
			'DarkTurquoise',
			'DarkViolet',
			'DeepPink',
			'DeepSkyBlue',
			'DimGray',
			'DodgerBlue',
			'FireBrick',
			'FloralWhite',
			'ForestGreen',
			'Fuchsia',
			'Gainsboro',
			'GhostWhite',
			'Gold',
			'GoldenRod',
			'Gray',
			'Green',
			'GreenYellow',
			'HoneyDew',
			'HotPink',
			'IndianRed',
			'Indigo',
			'Ivory',
			'Khaki',
			'Lavender',
			'LavenderBlush',
			'LawnGreen',
			'LemonChiffon',
			'LightBlue',
			'LightCoral',
			'LightCyan',
			'LightGoldenRodYellow',
			'LightGreen',
			'LightGrey',
			'LightPink',
			'LightSalmon',
			'LightSeaGreen',
			'LightSkyBlue',
			'LightSlateGray',
			'LightSteelBlue',
			'LightYellow',
			'Lime',
			'LimeGreen',
			'Linen',
			'Magenta',
			'Maroon',
			'MediumAquaMarine',
			'MediumBlue',
			'MediumOrchid',
			'MediumPurple',
			'MediumSeaGreen',
			'MediumSlateBlue',
			'MediumSpringGreen',
			'MediumTurquoise',
			'MediumVioletRed',
			'MidnightBlue',
			'MintCream',
			'MistyRose',
			'Moccasin',
			'NavajoWhite',
			'Navy',
			'OldLace',
			'Olive',
			'OliveDrab',
			'Orange',
			'OrangeRed',
			'Orchid',
			'PaleGoldenRod',
			'PaleGreen',
			'PaleTurquoise',
			'PaleVioletRed',
			'PapayaWhip',
			'PeachPuff',
			'Peru',
			'Pink',
			'Plum',
			'PowderBlue',
			'Purple',
			'Red',
			'RosyBrown',
			'RoyalBlue',
			'SaddleBrown',
			'Salmon',
			'SandyBrown',
			'SeaGreen',
			'SeaShell',
			'Sienna',
			'Silver',
			'SkyBlue',
			'SlateBlue',
			'SlateGray',
			'Snow',
			'SpringGreen',
			'SteelBlue',
			'Tan',
			'Teal',
			'Thistle',
			'Tomato',
			'Turquoise',
			'Violet',
			'Wheat',
			'White',
			'WhiteSmoke',
			'Yellow',
			'YellowGreen',
		];
	}
	
	/**
	 * Test on a valid color.
	 *
	 * @param string $color
	 *
	 * @return bool
	 */
	protected function isValidColor($color)
	{
		$regexp = '\(([01]?\d\d?|2[0-4]\d|25[0-5])\W+([01]?\d\d?|2[0-4]\d|25[0-5])\W+([01]?\d\d?|2[0-4]\d|25[0-5])\)';
		
		return preg_grep("/{$color}/i", $this->getColors()) || preg_match('/^#(\d|a|b|c|d|e|f){3}$/i', $color)
			|| preg_match('/^#(\d|a|b|c|d|e|f){6}$/i', $color) || preg_match('/^(rgb)'.$regexp.'$/i', $color)
			|| preg_match('/^(rgba)'.$regexp.'?\W+([01](\.\d+)?)\)$/i', $color);
	}
	
}
