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
use Arhitector\Transcoder\TimeInterval;
use Arhitector\Transcoder\TranscodeInterface;

/**
 * Apply fade-in/out effect to input audio.
 *
 * @package Arhitector\Transcoder\Filter
 */
class Fade implements AudioFilterInterface
{
	
	/**
	 * @var string The value for a fade-in effect.
	 */
	const FADE_IN = 'in';
	
	/**
	 * @var string The value for a fade-out effect.
	 */
	const FADE_OUT = 'out';
	
	/**
	 * @var string The triangular curve, linear slope.
	 */
	const CURVE_TRIANGLE = 'tri';
	
	/**
	 * @var string The quarter of sine wave curve.
	 */
	const CURVE_QUARTER = 'qsin';
	
	/**
	 * @var string The half of sine wave curve.
	 */
	const CURVE_HALF_SIN = 'hsin';
	
	/**
	 * @var string The exponential sine wave curve.
	 */
	const CURVE_EXP_SIN = 'esin';
	
	/**
	 * @var string The logarithmic curve.
	 */
	const CURVE_TRIANGULAR = 'log';
	
	/**
	 * @var string The inverted parabola.
	 */
	const CURVE_INV_PAR = 'ipar';
	
	/**
	 * @var string The quadratic curve.
	 */
	const CURVE_QUADRATE = 'qua';
	
	/**
	 * @var string The cubic curve.
	 */
	const CURVE_CUBE = 'cub';
	
	/**
	 * @var string The square root curve.
	 */
	const CURVE_SQUARE_ROOT = 'squ';
	
	/**
	 * @var string The cubic root curve.
	 */
	const CURVE_CUBIC_ROOT = 'cbr';
	
	/**
	 * @var string The parabola curve.
	 */
	const CURVE_PAR = 'par';
	
	/**
	 * @var string The exponential curve.
	 */
	const CURVE_EXP = 'exp';
	
	/**
	 * @var string The inverted quarter of sine wave curve.
	 */
	const CURVE_IQSIN = 'iqsin';
	
	/**
	 * @var string The inverted half of sine wave curve.
	 */
	const CURVE_IHSIN = 'ihsin';
	
	/**
	 * @var string The double-exponential seat curve.
	 */
	const CURVE_DOUBLE_EXP_SE = 'dese';
	
	/**
	 * @var string The double-exponential sigmoid curve.
	 */
	const CURVE_DOUBLE_EXP_SI = 'desi';
	
	/**
	 * @var TimeInterval Specify the start time of the fade effect.
	 */
	protected $startTime;
	
	/**
	 * @var string Specify the effect type.
	 */
	protected $effectType = self::FADE_IN;
	
	/**
	 * @var int Specify the number of the start sample.
	 */
	protected $startSample = 0;
	
	/**
	 * @var TimeInterval Specify the duration of the fade effect.
	 */
	protected $duration;
	
	/**
	 * @var int Specify the number of samples.
	 */
	protected $numberSamples = 44100;
	
	/**
	 * @var string The curve value for fade transition.
	 */
	protected $curve = self::CURVE_TRIANGULAR;
	
	/**
	 * Fade constructor.
	 *
	 * @param TimeInterval|int $startTime Specify the start time of the fade effect.
	 * @param TimeInterval|int $duration
	 * @param string           $effectType
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($startTime = 0, $duration = null, $effectType = null)
	{
		if ( ! $startTime instanceof TimeInterval)
		{
			$startTime = new TimeInterval($startTime);
		}
		
		$this->setStartTime($startTime);
		
		if ($duration !== null)
		{
			if ( ! $duration instanceof TimeInterval)
			{
				$duration = new TimeInterval($duration);
			}
			
			$this->setDuration($duration);
		}
		
		if ($effectType !== null)
		{
			$this->setEffectType($effectType);
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
		$options = [
			't'     => $this->getEffectType(),
			'st'    => (string) $this->getStartTime(),
			'curve' => $this->getCurve()
		];
		
		if ($this->getNumberSamples() !== null)
		{
			$options['ns'] = $this->getNumberSamples();
		}
		
		if ($this->getDuration() !== null)
		{
			$options['d'] = (string) $this->getDuration();
		}
		
		if ($this->getStartSample() !== null)
		{
			$options['ss'] = $this->getStartSample();
		}
		
		return [
			'filter:a' => [
				'afade' => http_build_query($options, null, ':')
			]
		];
	}
	
	/**
	 * Get the effect type value.
	 *
	 * @return string
	 */
	public function getEffectType()
	{
		return $this->effectType;
	}
	
	/**
	 * Specify the effect type, can be either in for fade-in, or out for a fade-out effect.
	 *
	 * @param string $effectType
	 *
	 * @return Fade
	 * @throws \InvalidArgumentException
	 */
	public function setEffectType($effectType)
	{
		$available = [self::FADE_IN, self::FADE_OUT];
		
		if ( ! is_scalar($effectType) || ! in_array($effectType, $available, false))
		{
			throw new \InvalidArgumentException(sprintf('Wrong effect type value for %s, available values are %s',
				(string) $effectType, implode(', ', $available)));
		}
		
		$this->effectType = (string) $effectType;
		
		return $this;
	}
	
	/**
	 * Get the start time.
	 *
	 * @return TimeInterval
	 */
	public function getStartTime()
	{
		return $this->startTime;
	}
	
	/**
	 * Specify the start time of the fade effect. If set this option is used instead of start_sample.
	 *
	 * @param TimeInterval $startTime
	 *
	 * @return Fade
	 */
	public function setStartTime(TimeInterval $startTime)
	{
		$this->startTime = $startTime;
		
		return $this;
	}
	
	/**
	 * Get the number of the start sample.
	 *
	 * @return int
	 */
	public function getStartSample()
	{
		return $this->startSample;
	}
	
	/**
	 * Specify the number of the start sample for starting to apply the fade effect.
	 *
	 * @param int $startSample
	 *
	 * @return Fade
	 * @throws \InvalidArgumentException
	 */
	public function setStartSample($startSample)
	{
		if ( ! is_numeric($startSample))
		{
			throw new \InvalidArgumentException('The start sample value must be a number.');
		}
		
		$this->startSample = (int) $startSample;
		
		return $this;
	}
	
	/**
	 * Get the duration value.
	 *
	 * @return TimeInterval
	 */
	public function getDuration()
	{
		return $this->duration;
	}
	
	/**
	 * Specify the duration of the fade effect. At the end of the fade-in effect the output audio will have the same
	 * volume as the input audio, at the end of the fade-out transition the output audio will be silence.
	 * If set this option is used instead of nb_samples.
	 *
	 * @param TimeInterval $duration
	 *
	 * @return Fade
	 */
	public function setDuration($duration)
	{
		$this->duration = $duration;
		
		return $this;
	}
	
	/**
	 * Get the number of samples value.
	 *
	 * @return int
	 */
	public function getNumberSamples()
	{
		return $this->numberSamples;
	}
	
	/**
	 * Specify the number of samples for which the fade effect has to last. At the end of the fade-in effect the output
	 * audio will have the same volume as the input audio, at the end of the fade-out transition the output audio will
	 * be silence.
	 *
	 * @param int $numberSamples
	 *
	 * @return Fade
	 * @throws \InvalidArgumentException
	 */
	public function setNumberSamples($numberSamples)
	{
		if ( ! is_numeric($numberSamples))
		{
			throw new \InvalidArgumentException('The number of samples value must be a number.');
		}
		
		$this->numberSamples = (int) $numberSamples;
		
		return $this;
	}
	
	/**
	 * Get the curve value.
	 *
	 * @return string
	 */
	public function getCurve()
	{
		return $this->curve;
	}
	
	/**
	 * Set curve for fade transition.
	 *
	 * @param string $curve
	 *
	 * @return Fade
	 * @throws \InvalidArgumentException
	 */
	public function setCurve($curve)
	{
		$available = [
			self::CURVE_TRIANGULAR,
			self::CURVE_QUARTER,
			self::CURVE_HALF_SIN,
			self::CURVE_EXP_SIN,
			self::CURVE_TRIANGULAR,
			self::CURVE_INV_PAR,
			self::CURVE_QUADRATE,
			self::CURVE_CUBE,
			self::CURVE_SQUARE_ROOT,
			self::CURVE_CUBIC_ROOT,
			self::CURVE_PAR,
			self::CURVE_EXP,
			self::CURVE_IQSIN,
			self::CURVE_IHSIN,
			self::CURVE_DOUBLE_EXP_SE,
			self::CURVE_DOUBLE_EXP_SI
		];
		
		if ( ! is_scalar($curve) || ! in_array($curve, $available, false))
		{
			throw new \InvalidArgumentException(sprintf('Wrong curve value for %s, available values are %s',
				(string) $curve, implode(', ', $available)));
		}
		
		$this->curve = (string) $curve;
		
		return $this;
	}
	
}
