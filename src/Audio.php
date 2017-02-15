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

use Arhitector\Transcoder\Exception\InvalidFilterException;
use Arhitector\Transcoder\Exception\TranscoderException;
use Arhitector\Transcoder\Filter\AudioFilterInterface;
use Arhitector\Transcoder\Filter\FilterInterface;
use Arhitector\Transcoder\Filter\Graph;
use Arhitector\Transcoder\Format\AudioFormat;
use Arhitector\Transcoder\Format\AudioFormatInterface;
use Arhitector\Transcoder\Format\FormatInterface;
use Arhitector\Transcoder\Service\ServiceFactory;
use Arhitector\Transcoder\Service\ServiceFactoryInterface;
use Arhitector\Transcoder\Stream\AudioStream;
use Arhitector\Transcoder\Stream\Collection;
use Arhitector\Transcoder\Stream\FrameStream;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Class Audio.
 *
 * @package Arhitector\Transcoder
 */
class Audio implements AudioInterface
{
	use TranscodeTrait {
		TranscodeTrait::getFormat as private _getFormat;
	}
	
	/**
	 * Audio constructor.
	 *
	 * @param string                  $filePath
	 * @param ServiceFactoryInterface $service
	 *
	 * @throws \Arhitector\Transcoder\Exception\TranscoderException
	 * @throws \InvalidArgumentException
	 */
	public function __construct($filePath, ServiceFactoryInterface $service = null)
	{
		$this->setFilePath($filePath);
		$this->setService($service ?: new ServiceFactory());
		
		/** @noinspection ExceptionsAnnotatingAndHandlingInspection */
		$demuxing = $this->getService()->getDecoderService()->demuxing($this);
		
		if (count($demuxing->streams) < 1 || ( ! $this->isSupportedFileType() && empty($demuxing->format['format'])))
		{
			throw new TranscoderException('File type unsupported or the file is corrupted.');
		}
		
		$this->_createCollections($demuxing);
		$this->filters = new Graph();
	}
	
	/**
	 * Gets the audio channels value.
	 *
	 * @return int
	 */
	public function getAudioChannels()
	{
		return $this->getFormat()->getChannels();
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
		return $this->getFormat()->getFrequency();
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
	 * @return AudioFormatInterface|\Arhitector\Transcoder\Format\FormatInterface
	 */
	public function getFormat()
	{
		return $this->_getFormat();
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
	 * @throws \Arhitector\Transcoder\Exception\TranscoderException
	 * @throws \InvalidArgumentException
	 */
	public function save(FormatInterface $format, $filePath, $overwrite = true)
	{
		if ( ! is_string($filePath) || empty($filePath))
		{
			throw new \InvalidArgumentException('File path must not be an empty string.');
		}
	
		if ( ! $overwrite && file_exists($filePath))
		{
			throw new TranscoderException('File path already exists.');
		}
		
		$options = ['output' => $filePath];
		
		foreach (clone $this->filters as $filter)
		{
			$options = array_merge_recursive($options, $filter->apply($this, $format));
		}
	
		/** @noinspection ExceptionsAnnotatingAndHandlingInspection */
		$processes = $this->getService()->getEncoderService()->transcoding($this, $format, $options);
		
		foreach ($processes as $process)
		{
			$process->start();
			
			if ($process->wait() !== 0)
			{
				throw new ProcessFailedException($process);
			}
		}
		
		return filesize($filePath);
	}
	
	/**
	 * Add a new filter.
	 *
	 * @param FilterInterface $filter
	 * @param int             $priority range 0-99.
	 *
	 * @return TranscodeInterface
	 * @throws \RangeException
	 * @throws InvalidFilterException
	 */
	public function addFilter(FilterInterface $filter, $priority = 0)
	{
		if ( ! $filter instanceof AudioFilterInterface)
		{
			throw new InvalidFilterException('Filter type is not supported.');
		}
		
		if ($priority > 99)
		{
			throw new \RangeException('Priority should be in the range from 0 to 99.');
		}
		
		$this->filters->insert($filter, $priority);
		
		return $this;
	}
	
	/**
	 * Reset filters.
	 *
	 * @return AudioInterface
	 */
	public function withoutFilters()
	{
		$self = clone $this;
		$self->filters = new Graph();
			
		return $self;
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
	
	/**
	 * Ensure streams etc.
	 *
	 * @param \stdClass $demuxing
	 *
	 * @throws \Arhitector\Transcoder\Exception\TranscoderException
	 * @throws \InvalidArgumentException
	 */
	protected function _createCollections($demuxing)
	{
		/** @var AudioFormatInterface $className */
		$className = $this->findFormatClass($demuxing->format['format'], AudioFormat::class);
		
		if ( ! $className instanceof AudioFormatInterface)
		{
			$className = AudioFormat::class;
		}
		
		$this->format = $className::fromArray(array_filter($demuxing->format, function ($value) {
			return $value !== null;
		}));
		
		$this->streams = new Collection(array_map(function ($parameters) {
			if ($parameters['type'] == 'audio')
			{
				$stream = AudioStream::create($this, $parameters);
				
				if ($stream->getChannels() !== null)
				{
					$this->getFormat()->setChannels($stream->getChannels());
				}
				
				if ($stream->getFrequency() !== null)
				{
					$this->getFormat()->setFrequency($stream->getFrequency());
				}
				
				$this->getFormat()->setAudioBitrate($stream->getBitrate());
				$this->getFormat()->setAudioCodec($stream->getCodec());
				
				return $stream;
			}
			
			if ($parameters['type'] == 'video')
			{
				return FrameStream::create($this, $parameters);
			}
			
			throw new TranscoderException('This stream unsupported.');
		}, $demuxing->streams));
	}
	
}
