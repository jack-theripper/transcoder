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
namespace Arhitector\Jumper\Filter;

use Arhitector\Jumper\Format\FormatInterface;
use Arhitector\Jumper\TranscoderInterface;

/**
 * Class AudioVolume.
 *
 * @package Arhitector\Jumper\Filter
 */
class Volume implements AudioFilterInterface
{
	
	/**
	 * @var string 8-bit fixed-point, this limits input sample format to U8, S16, and S32.
	 */
	const PRECISION_FIXED = 'fixed';
	
	/**
	 * @var string 32-bit floating-point, this limits input sample format to FLT. (default)
	 */
	const PRECISION_FLOAT = 'float';
	
	/**
	 * @var string 64-bit floating-point, this limits input sample format to DBL.
	 */
	const PRECISION_DOUBLE = 'double';
	
	/**
	 * @var mixed The audio volume expression.
	 */
	protected $volume = 1.0;
	
	/**
	 * @var string This parameter represents the mathematical precision.
	 */
	private $precision = self::PRECISION_FLOAT;
	
	/**
	 * AudioVolume constructor.
	 *
	 * @param float  $volume
	 * @param string $precision This parameter represents the mathematical precision.
	 */
	public function __construct($volume, $precision = null)
	{
		$this->volume = $volume;
		
		if ($precision !== null)
		{
			$this->precision = $precision;
		}
	}
	
	/**
	 * Apply filter.
	 *
	 * @param TranscoderInterface $media
	 * @param FormatInterface     $format
	 *
	 * @return array
	 */
	public function apply(TranscoderInterface $media, FormatInterface $format)
	{
		return [
			'filter:a' => [
				'volume' => http_build_query([
					'volume'    => $this->getVolume(),
					'precision' => $this->getPrecision()
				], null, ':')
			]
		];
	}
	
	/**
	 * Get the volume value.
	 *
	 * @return float
	 */
	public function getVolume()
	{
		return $this->volume;
	}
	
	/**
	 * Set the volume value.
	 *
	 * @param mixed $volume
	 *
	 * @return Volume
	 */
	public function setVolume($volume)
	{
		// TODO
		
		return $this;
	}
	
	/**
	 * Get the precision value.
	 *
	 * @return string
	 */
	public function getPrecision()
	{
		return $this->precision;
	}
	
	/**
	 * Set the precision value.
	 *
	 * @param string $precision
	 *
	 * @return Volume
	 * @throws \InvalidArgumentException
	 */
	public function setPrecision($precision)
	{
		$available = [self::PRECISION_FIXED, self::PRECISION_DOUBLE, self::PRECISION_FLOAT];
		
		if ( ! is_scalar($precision) || ! in_array($precision, $available, false))
		{
			throw new \InvalidArgumentException(sprintf('Wrong precision value for %s, available values are %s',
				(string) $precision, implode(', ', $available)));
		}
		
		$this->precision = (string) $precision;
		
		return $this;
	}
	
}
