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
use Arhitector\Jumper\Format\AudioFormat;
use Arhitector\Jumper\Format\AudioFormatInterface;
use Arhitector\Jumper\Format\FormatInterface;
use Arhitector\Jumper\Service\Decoder;
use Arhitector\Jumper\Service\Encoder;
use Arhitector\Jumper\Stream\AudioStream;
use Arhitector\Jumper\Stream\Collection;
use Arhitector\Jumper\Stream\StreamInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

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
	 * @var string The MIME Content-type for a file.
	 */
	protected $mimeType;
	
	/**
	 * @var Encoder
	 */
	protected $encoder;
	
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
		$this->encoder = new Encoder($options);
		
		/** @noinspection ExceptionsAnnotatingAndHandlingInspection */
		$demuxing = $this->decoder->demuxing($this);
		
		if (count($demuxing->streams) < 1 || ( ! $this->isSupportedFileType() && empty($demuxing->format['format'])))
		{
			throw new TranscoderException('File type unsupported or the file is corrupted.');
		}
		
		$this->format = new AudioFormat(null, $demuxing->format);
		$this->streams = new Collection(array_map(function ($parameters) {
			if ($parameters['type'] == 'audio')
			{
				$stream = new AudioStream($this, $parameters);
				
				if ($stream->getChannels() !== null)
				{
					$this->getFormat()->setAudioChannels($stream->getChannels());
				}
				
				if ($stream->getFrequency() !== null)
				{
					$this->getFormat()->setAudioFrequency($stream->getFrequency());
				}
				
				$this->getFormat()->setAudioBitrate($stream->getBitrate());
				$this->getFormat()->setAudioCodec($stream->getCodec());
				
				return $stream;
			}
			
			throw new TranscoderException('This stream unsupported.');
		}, $demuxing->streams));
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
	 * Gets the MIME Content-type value.
	 *
	 * @return string
	 */
	public function getMimeType()
	{
		return (string) $this->mimeType;
	}
	
	/**
	 * Transcoding.
	 *
	 * @param FormatInterface $format
	 * @param string          $filePath
	 * @param bool            $overwrite
	 *
	 * @return float|int Size of the new file or -1
	 * @throws \Symfony\Component\Process\Exception\RuntimeException
	 * @throws \Symfony\Component\Process\Exception\LogicException
	 * @throws \Symfony\Component\Process\Exception\ProcessFailedException
	 * @throws \Arhitector\Jumper\Exception\TranscoderException
	 * @throws \InvalidArgumentException
	 */
	public function save(FormatInterface $format, $filePath, $overwrite = true)
	{
		if ( ! $format instanceof AudioFormatInterface)
		{
			throw new \InvalidArgumentException('Format type is not supported.');
		}
		
		if ( ! is_string($filePath) || empty($filePath))
		{
			throw new \InvalidArgumentException('File path must not be an empty string.');
		}
		
		if ( ! $overwrite && file_exists($filePath))
		{
			throw new TranscoderException('File path already exists.');
		}
		
		$processes = $this->encoder->transcoding($this, $format, ['path' => $filePath]);
		
		foreach ($processes as $process)
		{
			if ($process->wait() !== 0)
			{
				throw new ProcessFailedException($process);
			}
		}
		
		return -1;
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
		$this->mimeType = mime_content_type($this->getFilePath());
		
		return $this;
	}
	
	/**
	 * It supports the type of media.
	 *
	 * @return bool
	 */
	protected function isSupportedFileType()
	{
		if (stripos($this->getMimeType(), 'audio/') !== 0)
		{
			return false;
		}
		
		return true;
	}
	
}