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
namespace Arhitector\Jumper\Format;

use Arhitector\Jumper\Codec;

/**
 * Class AudioFormat.
 *
 * @package Arhitector\Jumper\Format
 */
class AudioFormat implements AudioFormatInterface
{
	use FormatTrait;
	
	/**
	 * @var int Audio bitrate value.
	 */
	protected $audioBitrate = 0;
	
	/**
	 * @var int Audio channels value.
	 */
	protected $audioChannels;
	
	/**
	 * @var Codec Audio codec value.
	 */
	protected $audioCodec;
	
	/**
	 * @var int Audio sample frequency value.
	 */
	protected $audioFrequency;
	
	/**
	 * AudioFormat constructor.
	 *
	 * @param Codec|string $audioCodec
	 * @param array        $parameters
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($audioCodec = null, array $parameters = [])
	{
		if ($audioCodec !== null)
		{
			if ( ! $audioCodec instanceof Codec)
			{
				$audioCodec = new Codec($audioCodec, '');
			}
			
			$this->setAudioCodec($audioCodec);
		}
		
		$this->setAudioBitrate(128000);
		$this->resolveOptions($parameters);
	}
	
	/**
	 * Gets the audio channels value.
	 *
	 * @return int
	 */
	public function getAudioChannels()
	{
		return $this->audioChannels;
	}
	
	/**
	 * Sets the channels value.
	 *
	 * @param int $channels
	 *
	 * @return AudioFormatInterface
	 * @throws \InvalidArgumentException
	 */
	public function setAudioChannels($channels)
	{
		if ( ! is_numeric($channels) || $channels < 1)
		{
			throw new \InvalidArgumentException('Wrong audio channels value.');
		}
		
		$this->audioChannels = $channels;
		
		return $this;
	}
	
	/**
	 * Get audio codec.
	 *
	 * @return Codec
	 */
	public function getAudioCodec()
	{
		return $this->audioCodec;
	}
	
	/**
	 * Sets the audio codec, Should be in the available ones, otherwise an exception is thrown.
	 *
	 * @param Codec $audioCodec
	 *
	 * @return AudioFormatInterface
	 * @throws \InvalidArgumentException
	 */
	public function setAudioCodec(Codec $audioCodec)
	{
		if (class_parents($this) && ! in_array((string) $audioCodec, $this->getAvailableAudioCodecs(), false))
		{
			throw new \InvalidArgumentException(sprintf('Wrong audio codec value for %s, available values are %s',
				$audioCodec, implode(', ', $this->getAvailableAudioCodecs())));
		}
		
		$this->audioCodec = $audioCodec;
		
		return $this;
	}
	
	/**
	 * Get the audio bitrate value.
	 *
	 * @return int
	 */
	public function getAudioBitrate()
	{
		return $this->audioBitrate;
	}
	
	/**
	 * Sets the audio bitrate value.
	 *
	 * @param int $bitrate
	 *
	 * @return AudioFormatInterface
	 * @throws \InvalidArgumentException
	 */
	public function setAudioBitrate($bitrate)
	{
		if ( ! is_numeric($bitrate) || $bitrate < 0)
		{
			throw new \InvalidArgumentException('The audio bitrate value must be a integer type.');
		}
		
		$this->audioBitrate = (int) $bitrate;
		
		return $this;
	}
	
	/**
	 * Get frequency value.
	 *
	 * @return int
	 */
	public function getAudioFrequency()
	{
		return $this->audioFrequency;
	}
	
	/**
	 * Set frequency value.
	 *
	 * @param int $frequency
	 *
	 * @return AudioFormatInterface
	 * @throws \InvalidArgumentException
	 */
	public function setAudioFrequency($frequency)
	{
		if ( ! is_numeric($frequency) || $frequency < 1)
		{
			throw new \InvalidArgumentException('Wrong sample frequency value.');
		}
		
		$this->audioFrequency = $frequency;
		
		return $this;
	}
	
	/**
	 * Get available codecs.
	 *
	 * @return string[]
	 */
	public function getAvailableAudioCodecs()
	{
		return [];
	}
	
	/**
	 * Sets the options.
	 *
	 * @param array $options
	 *
	 * @return AudioFormat
	 * @throws \InvalidArgumentException
	 */
	protected function resolveOptions(array $options)
	{
		if (isset($options['audio_codec']))
		{
			$this->setAudioCodec($options['audio_codec']);
		}
		
		if (isset($options['audio_bitrate']))
		{
			$this->setAudioBitrate((int) $options['audio_bitrate']);
		}
		
		if ( ! empty($options['channels']))
		{
			$this->setAudioChannels((int) $options['channels']);
		}
		
		if (isset($options['frequency']))
		{
			$this->setAudioFrequency((int) $options['frequency']);
		}
		
		if (isset($options['duration']))
		{
			$this->setDuration((float) $options['duration']);
		}
		
		if (isset($options['tags']) && is_array($options['tags']))
		{
			$this->tags = array_filter($options['tags'], 'is_scalar');
		}
		
		return $this;
	}
	
}
