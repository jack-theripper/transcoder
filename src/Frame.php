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
use Arhitector\Transcoder\Filter\FilterInterface;
use Arhitector\Transcoder\Format\FormatInterface;
use Arhitector\Transcoder\Format\FrameFormat;
use Arhitector\Transcoder\Format\FrameFormatInterface;
use Arhitector\Transcoder\Stream\StreamInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Class Frame.
 *
 * @package Arhitector\Transcoder
 */
class Frame implements FrameInterface
{
	use TranscodeTrait {
		TranscodeTrait::getFormat as private _getFormat;
	}
	
	/**
	 * Returns the video codec.
	 *
	 * @return Codec|null
	 */
	public function getVideoCodec()
	{
		return $this->getFormat()->getVideoCodec();
	}
	
	/**
	 * Get width value.
	 *
	 * @return int
	 */
	public function getWidth()
	{
		return $this->getFormat()->getWidth();
	}
	
	/**
	 * Get height value.
	 *
	 * @return int
	 */
	public function getHeight()
	{
		return $this->getFormat()->getHeight();
	}
	
	/**
	 * Get current format.
	 *
	 * @return FrameFormatInterface|\Arhitector\Transcoder\Format\FormatInterface
	 */
	public function getFormat()
	{
		return $this->_getFormat();
	}
	
	/**
	 * Get duration value.
	 *
	 * @return float
	 */
	public function getDuration()
	{
		return 0.0;
	}
	
	/**
	 * Transcoding.
	 *
	 * @param FormatInterface $format
	 * @param string          $filePath
	 * @param bool            $overwrite
	 *
	 * @return TranscodeInterface
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
				if ( ! $process->isTerminated() && $process->run(new EventProgress($pass, $format)) !== 0)
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
	 * @throws InvalidFilterException
	 */
	public function addFilter(FilterInterface $filter, $priority = 0)
	{
		// TODO: Implement addFilter() method.
	}
	
	/**
	 * Reset filters.
	 *
	 * @return TranscodeInterface
	 */
	public function withoutFilters()
	{
		// TODO: Implement withoutFilters() method.
	}
	
	/**
	 * It supports the type of media.
	 *
	 * @return bool
	 */
	protected function isSupportedFileType()
	{
		return ! (stripos($this->getMimeType(), 'image/') !== 0);
	}
	
	/**
	 * Creates an instance of the format from the internal type.
	 *
	 * @param array $formatArray
	 *
	 * @return FormatInterface
	 * @throws \InvalidArgumentException
	 * @throws \Arhitector\Transcoder\Exception\TranscoderException
	 */
	protected function createFormat(array $formatArray)
	{
		$format = $this->findFormatClass($formatArray['format'], FrameFormat::class);
		
		if ( ! is_subclass_of($format, FrameFormatInterface::class))
		{
			throw new TranscoderException('Invalid format type.');
		}
		
		if ($stream = $this->getStreams(StreamInterface::STREAM_FRAME)->getFirst())
		{
			foreach ($stream->toArray() as $key => $value)
			{
				$formatArray[$key] = $value;
				
				if ($key == 'codec')
				{
					$formatArray['video_codec'] = $value;
				}
			}
		}
		
		if (isset($formatArray['codecs']) && is_array($formatArray['codecs']))
		{
			$formatArray['available_video_codecs'] = array_keys(array_filter($formatArray['codecs'], function ($mask) {
				return $mask & 2;
			}));
		}
		
		return $format::fromArray(array_filter($formatArray, function ($value) {
			return $value !== null;
		}));
	}
	
}
