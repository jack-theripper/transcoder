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
namespace Arhitector\Transcoder\Stream;

/**
 * Class AudioStream.
 *
 * @package Arhitector\Transcoder\Stream
 */
class AudioStream implements AudioStreamInterface
{
	use StreamTrait;
	
	/**
	 * @var int Audio channels value.
	 */
	protected $channels;
	
	/**
	 * @var int Sample rate value.
	 */
	protected $frequency;
	
	/**
	 * Get channels value.
	 *
	 * @return int
	 */
	public function getChannels()
	{
		return $this->channels;
	}
	
	/**
	 * Get sample rate value.
	 *
	 * @return string
	 */
	public function getFrequency()
	{
		return $this->frequency;
	}
	
	/**
	 * Sets the channels value.
	 *
	 * @param  int $channels
	 *
	 * @return AudioStream
	 * @throws \InvalidArgumentException
	 */
	protected function setChannels($channels)
	{
		if ( ! is_numeric($channels) || $channels < 1)
		{
			throw new \InvalidArgumentException('Wrong channels value.');
		}
		
		$this->channels = $channels;
		
		return $this;
	}
	
	/**
	 * Set sample rate value.
	 *
	 * @param  int $sampleRate
	 *
	 * @return AudioStream
	 * @throws \InvalidArgumentException
	 */
	protected function setFrequency($sampleRate)
	{
		if ( ! is_numeric($sampleRate) || $sampleRate < 0)
		{
			throw new \InvalidArgumentException('Wrong sample rate value.');
		}
		
		$this->frequency = (int) $sampleRate;
		
		return $this;
	}
	
}
