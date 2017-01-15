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
namespace Arhitector\Jumper;

use Arhitector\Jumper\Exception\TranscoderException;
use Arhitector\Jumper\Format\AudioFormatInterface;
use Arhitector\Jumper\Service\Decoder;
use Arhitector\Jumper\Stream\Collection;
use Arhitector\Jumper\Stream\StreamInterface;

/**
 * Class Audio.
 *
 * @package Arhitector\Jumper
 */
class Audio implements AudioInterface
{
	
	/**
	 * @var string  The full path to the file.
	 */
	protected $filePath;
	
	/**
	 * @var AudioFormatInterface
	 */
	protected $format;
	
	/**
	 * @var Collection
	 */
	protected $streams;
	
	/**
	 * @var Decoder
	 */
	protected $decoder;
	
	/**
	 * Audio constructor.
	 *
	 * @param string $filePath
	 * @param array  $options
	 *
	 * @throws \InvalidArgumentException
	 * @throws \Arhitector\Jumper\Exception\TranscoderException
	 * @throws \Arhitector\Jumper\Exception\ExecutableNotFoundException
	 */
	public function __construct($filePath, array $options = [])
	{
		$this->setFilePath($filePath);
		$this->decoder = new Decoder($options);
		
		$demuxing = $this->decoder->demuxing($this);
		
		if (count($demuxing->streams) < 1 || empty($demuxing->format['format']))
		{
			throw new TranscoderException('File type unsupported or the file is corrupted.');
		}
	}
	
	/**
	 * Gets the audio channels value.
	 *
	 * @return int
	 */
	public function getAudioChannels()
	{
		return $this->getFormat()->getAudioChannels();
	}
	
	/**
	 * Gets the audio kilo bitrate value.
	 *
	 * @return int
	 */
	public function getAudioKiloBitrate()
	{
		return (int) ($this->getFormat()->getAudioBitrate() / 1000);
	}
	
	/**
	 * Returns the audio codec.
	 *
	 * @return Codec|null
	 */
	public function getAudioCodec()
	{
		return $this->getFormat()->getAudioCodec();
	}
	
	/**
	 * Get sample frequency value.
	 *
	 * @return int
	 */
	public function getFrequency()
	{
		return $this->getFormat()->getAudioFrequency();
	}
	
	/**
	 * Get the full path to the file.
	 *
	 * @return string
	 */
	public function getFilePath()
	{
		return $this->filePath;
	}
	
	/**
	 * Get duration value.
	 *
	 * @return float
	 */
	public function getDuration()
	{
		return $this->getFormat()->getDuration();
	}
	
	/**
	 * Get current format.
	 *
	 * @return AudioFormatInterface
	 * @throws TranscoderException
	 */
	public function getFormat()
	{
		return $this->format;
	}
	
	/**
	 * Get a list of streams.
	 *
	 * @return Collection|StreamInterface[]
	 * @throws TranscoderException
	 */
	public function getStreams()
	{
		return $this->streams;
	}
	
	/**
	 * Set file path.
	 *
	 * @param string $filePath
	 *
	 * @return Audio
	 * @throws \InvalidArgumentException
	 * @throws TranscoderException
	 */
	protected function setFilePath($filePath)
	{
		if ( ! is_string($filePath))
		{
			throw new \InvalidArgumentException('File path must be a string type.');
		}
		
		$filePath = realpath($filePath);
		
		if ( ! is_file($filePath))
		{
			throw new TranscoderException('File path not found.');
		}
		
		$this->filePath = $filePath;
		
		return $this;
	}
	
}