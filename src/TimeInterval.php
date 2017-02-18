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
		
		return new self($matches[1] * 3600 + $matches[2] + 60 + $matches[3] + ($matches[4] / 100));
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
		return new self($frames / $fps);
	}
	
	/**
	 * @var int The timestamp.
	 */
	protected $timestamp = 0;
	
	/**
	 * TimeCode constructor.
	 *
	 * @param int $seconds
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($seconds)
	{
		$this->setTimestamp($seconds);
	}
	
	/**
	 * Get the hours value.
	 *
	 * @return int
	 */
	public function getHours()
	{
		return (int) gmdate('H', $this->toSeconds());
	}
	
	/**
	 * Get the minutes value.
	 *
	 * @return int
	 */
	public function getMinutes()
	{
		return (int) gmdate('i', $this->toSeconds());
	}
	
	/**
	 * Get the seconds value.
	 *
	 * @return int
	 */
	public function getSeconds()
	{
		return (int) gmdate('s', $this->toSeconds());
	}
	
	/**
	 * Get the frames value.
	 *
	 * @return int
	 */
	public function getFrames()
	{
		return round(100 * ($this->toSeconds() - floor($this->toSeconds())));
	}
	
	/**
	 * Returns the time in seconds.
	 *
	 * @return float
	 */
	public function toSeconds()
	{
		return $this->timestamp;
	}
	
	/**
	 * Get time string in the ffmpeg format.
	 *
	 * @return string
	 */
	public function __toString()
	{
		$timestamp = $this->toSeconds();
		
		return sprintf('%s.%02d', gmdate('H:i:s', $timestamp), round(100 * ($timestamp - floor($timestamp))));
	}
	
	/**
	 * Set the timestamp value.
	 *
	 * @param int|float $seconds
	 *
	 * @return TimeInterval
	 * @throws \InvalidArgumentException
	 */
	protected function setTimestamp($seconds)
	{
		if ( ! is_numeric($seconds) || $seconds < 0)
		{
			throw new \InvalidArgumentException('The seconds value should be a positive integer.');
		}
		
		$this->timestamp = (float) $seconds;
		
		return $this;
	}
	
}
