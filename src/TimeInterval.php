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
 * Class TimeInterval.
 *
 * @package Arhitector\Transcoder
 */
class TimeInterval
{
	
	/**
	 * Create TimeInterval from string.
	 *
	 * @param string $string For example, 'hh:mm:ss:frame', 'hh:mm:ss,frame', 'hh:mm:ss.frame'
	 *
	 * @return TimeInterval
	 * @throws \InvalidArgumentException
	 */
	public static function fromString($string)
	{
		if ( ! preg_match('/^(\d+):(\d+):(\d+)[:,\.]{1}(\d+)$/', $string, $matches))
		{
			throw new \InvalidArgumentException('Time string has an unsupported format.');
		}
		
		return new self($matches[1], $matches[2], $matches[3], $matches[4]);
	}
	
	/**
	 * Create TimeCode from seconds.
	 *
	 * @param float|int $seconds
	 *
	 * @return TimeInterval
	 * @throws \InvalidArgumentException
	 */
	public static function fromSeconds($seconds)
	{
		if ( ! is_numeric($seconds) || $seconds < 0)
		{
			throw new \InvalidArgumentException('Seconds value must be a positive numeric.');
		}
		
		$split = explode(':', gmdate('H:i:s', $seconds));
		
		return new self($split[0], $split[1], $split[2], round(100 * ($seconds - floor($seconds))));
	}
	
	/**
	 * Create TimeCode from frames.
	 *
	 * @param int   $frames
	 * @param float $fps
	 *
	 * @return TimeInterval
	 * @throws \InvalidArgumentException
	 */
	public static function fromFrame($frames, $fps)
	{
		return self::fromSeconds($frames / $fps);
	}
	
	/**
	 * @var int
	 */
	protected $hours = 0;
	
	/**
	 * @var int
	 */
	protected $minutes = 0;
	
	/**
	 * @var int
	 */
	protected $seconds = 0;
	
	/**
	 * @var int
	 */
	protected $frames = 0;
	
	/**
	 * TimeCode constructor.
	 *
	 * @param int $hours
	 * @param int $minutes
	 * @param int $seconds
	 * @param int $frames
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($hours, $minutes, $seconds, $frames = 0)
	{
		$this->setHours($hours);
		$this->setMinutes($minutes);
		$this->setSeconds($seconds);
		$this->setFrames($frames);
	}
	
	/**
	 * Get hours value.
	 *
	 * @return int
	 */
	public function getHours()
	{
		return $this->hours;
	}
	
	/**
	 * Get minutes value.
	 *
	 * @return int
	 */
	public function getMinutes()
	{
		return $this->minutes;
	}
	
	/**
	 * Get seconds value.
	 *
	 * @return int
	 */
	public function getSeconds()
	{
		return $this->seconds;
	}
	
	/**
	 * Get frames value.
	 *
	 * @return int
	 */
	public function getFrames()
	{
		return $this->frames;
	}
	
	/**
	 * Get time string in the format.
	 *
	 * @return string
	 */
	public function toString()
	{
		return sprintf('%02d:%02d:%02d.%02d', $this->getHours(), $this->getMinutes(), $this->getSeconds(),
			$this->getFrames());
	}
	
	/**
	 * Set hours value.
	 *
	 * @param int $hours
	 *
	 * @return TimeInterval
	 * @throws \InvalidArgumentException
	 */
	protected function setHours($hours)
	{
		if ($hours < 0)
		{
			throw new \InvalidArgumentException('Hours value should be a positive integer.');
		}
		
		$this->hours = (int) $hours;
		
		return $this;
	}
	
	/**
	 * Set minutes value.
	 *
	 * @param int $minutes
	 *
	 * @return TimeInterval
	 * @throws \InvalidArgumentException
	 */
	protected function setMinutes($minutes)
	{
		if ($minutes < 0)
		{
			throw new \InvalidArgumentException('Minutes value should be a positive integer.');
		}
		
		$this->minutes = (int) $minutes;
		
		return $this;
	}
	
	/**
	 * Set seconds value.
	 *
	 * @param int $seconds
	 *
	 * @return TimeInterval
	 * @throws \InvalidArgumentException
	 */
	protected function setSeconds($seconds)
	{
		if ($seconds < 0)
		{
			throw new \InvalidArgumentException('Seconds value should be a positive integer.');
		}
		
		$this->seconds = (int) $seconds;
		
		return $this;
	}
	
	/**
	 * Set frames value.
	 *
	 * @param int $frames
	 *
	 * @return TimeInterval
	 * @throws \InvalidArgumentException
	 */
	protected function setFrames($frames)
	{
		if ($frames < 0)
		{
			throw new \InvalidArgumentException('Frames value should be a positive integer.');
		}
		
		$this->frames = (int) $frames;
		
		return $this;
	}
	
}
