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

use Arhitector\Transcoder\Event\EventProgress;
use Arhitector\Transcoder\Exception\ExecutionFailureException;
use Arhitector\Transcoder\Exception\InvalidFilterException;
use Arhitector\Transcoder\Exception\TranscoderException;
use Arhitector\Transcoder\Filter\AudioFilterInterface;
use Arhitector\Transcoder\Filter\FilterInterface;
use Arhitector\Transcoder\Filter\Graph;
use Arhitector\Transcoder\Format\AudioFormat;
use Arhitector\Transcoder\Format\AudioFormatInterface;
use Arhitector\Transcoder\Format\FormatInterface;
use Arhitector\Transcoder\Stream\AudioStreamInterface;
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
		return $this->getFormat()->getDuration()->toSeconds();
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
	 * @return Audio
	 * @throws \Arhitector\Transcoder\Exception\ExecutionFailureException
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
			$options = array_replace_recursive($options, $filter->apply($this, $format));
		}
	
		/** @noinspection ExceptionsAnnotatingAndHandlingInspection */
		$processes = $this->getService()->getEncoderService()->transcoding($this, $format, $options);
		
		if ($format->emit('before')->isPropagationStopped())
		{
			return $this;
		}
		
		try
		{
			foreach ($processes as $pass => $process)
			{
				if ( ! $process->isTerminated() && $process->run(new EventProgress($pass, $this->getFormat())) !== 0)
				{
					throw new ProcessFailedException($process);
				}
			}
			
			$format->emit('success');
		}
		catch (ProcessFailedException $exc)
		{
			$format->emit('failure');
			
			throw new ExecutionFailureException($exc->getMessage(), $exc->getProcess(), $exc->getCode(), $exc);
		}
		finally
		{
			$format->emit('after');
		}
		
		return $this;
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
	 * Creates an instance of the format from the internal type.
	 *
	 * @param array $formatArray
	 *
	 * @return AudioFormatInterface
	 * @throws \InvalidArgumentException
	 * @throws \Arhitector\Transcoder\Exception\TranscoderException
	 */
	protected function createFormat(array $formatArray)
	{
		$format = $this->findFormatClass($formatArray['format'], AudioFormat::class);
		
		if ( ! is_subclass_of($format, AudioFormatInterface::class))
		{
			throw new TranscoderException('Invalid format type.');
		}
		
		foreach ($this->getStreams(self::STREAM_AUDIO | self::STREAM_FRAME) as $stream)
		{
			$prefix = $stream instanceof AudioStreamInterface ? 'audio_' : 'video_';
			
			foreach ($stream->toArray() as $key => $value)
			{
				if ($key != 'metadata')
				{
					$formatArray[$key] = $value;
				}
				
				if (in_array($key, ['codec', 'bitrate'], false))
				{
					$formatArray[$prefix.$key] = $value;
				}
			}
		}
		
		return $format::fromArray(array_filter($formatArray, function ($value) {
			return $value !== null;
		}));
	}
	
}
