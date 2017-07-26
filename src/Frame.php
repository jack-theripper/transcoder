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
use Arhitector\Transcoder\Filter\FrameFilterInterface;
use Arhitector\Transcoder\Filter\SimpleFilter;
use Arhitector\Transcoder\Format\FormatInterface;
use Arhitector\Transcoder\Format\FrameFormat;
use Arhitector\Transcoder\Format\FrameFormatInterface;
use Arhitector\Transcoder\Format\VideoFormatInterface;
use Arhitector\Transcoder\Stream\Collection;
use Arhitector\Transcoder\Stream\FrameStream;
use Arhitector\Transcoder\Stream\VideoStream;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Class Frame.
 *
 * @package Arhitector\Transcoder
 */
class Frame implements FrameInterface
{
	use TranscodeTrait;
	
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
	 * @return FrameFormatInterface
	 */
	public function getFormat()
	{
		return $this->format;
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
	 * @throws \InvalidArgumentException
	 * @throws InvalidFilterException
	 */
	public function addFilter(FilterInterface $filter, $priority = 0)
	{
		if ( ! $filter instanceof FrameFilterInterface)
		{
			throw new InvalidFilterException('Filter type is not supported.');
		}
		
		$this->filters->insert($filter, $priority);
		
		return $this;
	}
	
	/**
	 * Initializing.
	 *
	 * @param \StdClass $demuxing
	 *
	 * @return void
	 */
	protected function initialize(\StdClass $demuxing)
	{
		/** @var FrameFormatInterface $format */
		$format = $this->findFormatClass($demuxing->format['format'], FrameFormat::class);
		
		if ( ! is_subclass_of($format, FrameFormatInterface::class))
		{
			throw new TranscoderException(sprintf('This format unsupported in the "%s" wrapper.', __CLASS__));
		}
		
		$streams =  new Collection();
		
		foreach ($demuxing->streams as $number => $stream)
		{
			if (isset($stream['type']) && strtolower($stream['type']) == 'video')
			{
				$streams[$number] = is_subclass_of($format, VideoFormatInterface::class)
					? VideoStream::create($this, $stream) : FrameStream::create($this, $stream);
			}
		}
		
		$this->setStreams($streams);
		
		if ($stream = $this->getStreams(self::STREAM_FRAME)->getFirst())
		{
			foreach ($stream->toArray() as $key => $value)
			{
				if ($key != 'metadata')
				{
					$demuxing->format[$key] = $value;
				}
				
				if ($key == 'codec')
				{
					$demuxing->format['video_codec'] = $value;
				}
			}
		}
		
		if (isset($demuxing->format['codecs']) && is_array($demuxing->format['codecs']))
		{
			$demuxing->format['available_video_codecs'] = array_keys(array_filter($demuxing->format['codecs'], function ($mask) {
				return $mask & 2;
			}));
		}
		
		$this->setFormat($format::fromArray(array_filter($demuxing->format, function ($value) {
			return $value !== null;
		})));
		
		if ($this->getFormat() instanceof VideoFormatInterface)
		{
			$this->addFilter(new SimpleFilter([
				'frames'     => 1,
				'seek_start' => 0,
			]), 0);
		}
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
	
}
