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
use Arhitector\Jumper\TranscodeInterface;

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
	 * @const string Remove ReplayGain side data, ignoring its contents.
	 */
	const REPLAY_GAIN_DROP = 'drop';
	
	/**
	 * @const string Ignore ReplayGain side data, but leave it in the frame.
	 */
	const REPLAY_GAIN_IGNORE = 'ignore';
	
	/**
	 * @const string Prefer the track gain, if present.
	 */
	const REPLAY_GAIN_TRACK = 'track';
	
	/**
	 * @const string Prefer the album gain, if present.
	 */
	const REPLAY_GAIN_ALBUM = 'album';
	
	/**
	 * @const string Only evaluate expression once during the filter initialization, or when the ‘volume’ command is
	 *        sent.
	 */
	const EVAL_ONCE = 'once';
	
	/**
	 * @const string Evaluate expression for each incoming frame.
	 */
	const EVAL_FRAME = 'frame';
	
	/**
	 * @var mixed The audio volume expression.
	 */
	protected $volume = 1.0;
	
	/**
	 * @var string This parameter represents the mathematical precision.
	 */
	protected $precision = self::PRECISION_FLOAT;
	
	/**
	 * @var string Choose the behaviour on encountering ReplayGain side data in input frames.
	 */
	protected $replayGain = self::REPLAY_GAIN_DROP;
	
	/**
	 * @var float Pre-amplification gain in dB to apply to the selected replayGain gain.
	 */
	protected $replayGainPreamp = 0.0;
	
	/**
	 * @var string Set when the volume expression is evaluated.
	 */
	protected $eval = self::EVAL_ONCE;
	
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
	 * @param TranscodeInterface $media
	 * @param FormatInterface    $format
	 *
	 * @return array
	 */
	public function apply(TranscodeInterface $media, FormatInterface $format)
	{
		return [
			'filter:a' => [
				'volume' => http_build_query([
					'volume'            => $this->getVolume(),
					'precision'         => $this->getPrecision(),
					'replaygain'        => $this->getReplayGain(),
					'replaygain_preamp' => $this->getReplayGainPreamp(),
					'eval'              => $this->getEval()
				], null, ':')
			]
		];
	}
	
	/**
	 * Get the volume value.
	 *
	 * @return float|string
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
	 * @throws \InvalidArgumentException
	 */
	public function setVolume($volume)
	{
		if ( ! is_scalar($volume))
		{
			throw new \InvalidArgumentException('The volume value must be a scalar.');
		}
		
		$this->volume = $volume;
		
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
	
	/**
	 * Get the replay gain value.
	 *
	 * @return string
	 */
	public function getReplayGain()
	{
		return $this->replayGain;
	}
	
	/**
	 * Set the replay gain value.
	 *
	 * @param string $replayGain
	 *
	 * @return Volume
	 * @throws \InvalidArgumentException
	 */
	public function setReplayGain($replayGain)
	{
		$values = [self::REPLAY_GAIN_DROP, self::REPLAY_GAIN_IGNORE, self::REPLAY_GAIN_TRACK, self::REPLAY_GAIN_ALBUM];
		
		if ( ! is_scalar($replayGain) || ! in_array($replayGain, $values, false))
		{
			throw new \InvalidArgumentException(sprintf('Wrong replayGain value for %s, available values are %s',
				(string) $replayGain, implode(', ', $values)));
		}
		
		$this->replayGain = (string) $replayGain;
		
		return $this;
	}
	
	/**
	 * Get the pre-amplification gain value.
	 *
	 * @return float
	 */
	public function getReplayGainPreamp()
	{
		return $this->replayGainPreamp;
	}
	
	/**
	 * Set the pre-amplification gain value.
	 *
	 * @param float $replayGainPreamp
	 *
	 * @return Volume
	 * @throws \InvalidArgumentException
	 */
	public function setReplayGainPreamp($replayGainPreamp)
	{
		if ( ! is_float($replayGainPreamp))
		{
			throw new \InvalidArgumentException('The replayGainPream must be a float type.');
		}
		
		$this->replayGainPreamp = $replayGainPreamp;
		
		return $this;
	}
	
	/**
	 * Get the eval value.
	 *
	 * @return string
	 */
	public function getEval()
	{
		return $this->eval;
	}
	
	/**
	 * Set the eval value.
	 *
	 * @param string $eval
	 *
	 * @return Volume
	 * @throws \InvalidArgumentException
	 */
	public function setEval($eval)
	{
		$available = [self::EVAL_ONCE, self::EVAL_FRAME];
		
		if ( ! is_scalar($eval) || ! in_array($eval, $available, false))
		{
			throw new \InvalidArgumentException(sprintf('Wrong eval value for %s, available values are %s',
				(string) $eval, implode(', ', $available)));
		}
		
		$this->eval = (string) $eval;
		
		return $this;
	}
	
}
