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
namespace Arhitector\Transcoder\Format;

use Arhitector\Transcoder\Codec;

/**
 * Class AudioFormat.
 *
 * @package Arhitector\Transcoder\Format
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
	 * @var string[] The list of available audio codecs.
	 */
	protected $audioAvailableCodecs = [];
	
	/**
	 * AudioFormat constructor.
	 *
	 * @param Codec|string $audioCodec
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($audioCodec = null)
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
	}
	
	/**
	 * Gets the audio channels value.
	 *
	 * @return int
	 */
	public function getChannels()
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
	public function setChannels($channels)
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
	public function getFrequency()
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
	public function setFrequency($frequency)
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
		return $this->audioAvailableCodecs;
	}
	
	/**
	 * Returns the number of passes.
	 *
	 * @return int
	 */
	public function getPasses()
	{
		return 1;
	}
	
	/**
	 * Sets the list of available audio codecs.
	 *
	 * @param array $codecs
	 *
	 * @return AudioFormat
	 */
	protected function setAvailableAudioCodecs(array $codecs)
	{
		$this->audioAvailableCodecs = array_map('strval', $codecs);
		
		return $this;
	}
	
}
