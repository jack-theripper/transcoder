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
namespace Arhitector\Jumper\Stream;

use Arhitector\Jumper\TranscoderInterface;

/**
 * Class AudioStream.
 *
 * @package Arhitector\Jumper\Stream
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
	 * AudioStream constructor.
	 *
	 * @param TranscoderInterface $media
	 * @param array               $parameters
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct(TranscoderInterface $media, array $parameters)
	{
		$this->filePath = $media->getFilePath();
		
		if ( ! isset($parameters['index']) || $parameters['index'] < 0)
		{
			throw new \InvalidArgumentException('The index value is wrong.');
		}
		
		$this->setIndex($parameters['index']);
		
		if (isset($parameters['codec']))
		{
			$this->setCodec($parameters['codec']);
		}
		
		if (isset($parameters['profile']))
		{
			$this->setProfile((string) $parameters['profile']);
		}
		
		if (isset($parameters['bit_rate']))
		{
			$this->setBitrate((int) $parameters['bit_rate']);
		}
		
		if (isset($parameters['start_time']))
		{
			$this->setStartTime((float) $parameters['start_time']);
		}
		
		if (isset($parameters['frequency']))
		{
			$this->setFrequency((int) $parameters['frequency']);
		}
		
		if (isset($parameters['channels']))
		{
			$this->setChannels((int) $parameters['channels']);
		}
		
		if (isset($parameters['duration']))
		{
			$this->setDuration((float) $parameters['duration']);
		}
	}
	
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