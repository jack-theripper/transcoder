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
use Arhitector\Transcoder\Filter\FrameFilterInterface;
use Arhitector\Transcoder\Filter\SimpleFilter;
use Arhitector\Transcoder\Format\FormatInterface;
use Arhitector\Transcoder\Format\VideoFormat;
use Arhitector\Transcoder\Format\VideoFormatInterface;
use Arhitector\Transcoder\Stream\AudioStream;
use Arhitector\Transcoder\Stream\Collection;
use Arhitector\Transcoder\Stream\FrameStream;
use Arhitector\Transcoder\Stream\SubtitleStream;
use Arhitector\Transcoder\Stream\VideoStream;
use Arhitector\Transcoder\Stream\VideoStreamInterface;

/**
 * Class Video.
 *
 * @package Arhitector\Transcoder
 */
class Video extends Audio implements VideoInterface
{
	
	/**
	 * Get current format.
	 *
	 * @return VideoFormatInterface|\Arhitector\Transcoder\Format\FormatInterface
	 */
	public function getFormat()
	{
		return parent::getFormat();
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
	 * Gets the bitrate value.
	 *
	 * @return int
	 */
	public function getKiloBitrate()
	{
		return (int) ($this->getFormat()->getVideoBitrate() / 1000);
	}
	
	/**
	 * Get frame rate value.
	 *
	 * @return float
	 */
	public function getFrameRate()
	{
		return $this->getFormat()->getFrameRate();
	}
	
	/**
	 * Add a new filter.
	 *
	 * @param FilterInterface $filter
	 * @param int             $priority range 0-99.
	 *
	 * @return TranscodeInterface
	 * @throws \InvalidArgumentException
	 * @throws \RangeException
	 * @throws InvalidFilterException
	 */
	public function addFilter(FilterInterface $filter, $priority = 0)
	{
		if ( ! $filter instanceof FrameFilterInterface && ! $filter instanceof AudioFilterInterface)
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
	 * Return a new Frame from by time interval.
	 *
	 * @param TimeInterval|int|float $interval
	 *
	 * @return Frame
	 */
	public function getFrame($interval)
	{
		if ( ! $interval instanceof TimeInterval)
		{
			$interval = new TimeInterval($interval);
		}
		
		if ($interval->getSeconds() > $this->getDuration())
		{
			throw new \OutOfRangeException('The interval may not be a more than '.$this->getDuration());
		}
		
		$frame = new Frame($this->getFilePath(), $this->getService());
		$frame->addFilter(new SimpleFilter(['seek_start' => $interval->__toString()]));
		
		return $frame;
	}
	
	/**
	 * It supports the type of media.
	 *
	 * @return bool
	 */
	protected function isSupportedFileType()
	{
		return ! (stripos($this->getMimeType(), 'video/') !== 0);
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
		$format = $this->findFormatClass($demuxing->format['format'], VideoFormat::class);
		
		if ( ! is_subclass_of($format, VideoFormatInterface::class))
		{
			throw new TranscoderException(sprintf('This format unsupported in the "%s" wrapper.', __CLASS__));
		}
		
		$streams = [];
		
		foreach ($demuxing->streams as $number => $stream)
		{
			$stream['type'] = isset($stream['type']) ? strtolower($stream['type']) : null;
			
			$stream['type'] = isset($stream['type']) ? strtolower($stream['type']) : null;
			
			if ($stream['type'] == 'audio')
			{
				$streams[] = AudioStream::create($this, $stream);
			}
			else if ($stream['type'] == 'video')
			{
				if ($this instanceof AudioInterface && $this instanceof VideoInterface)
				{
					$streams[] = VideoStream::create($this, $stream);
				}
				else
				{
					$streams[] = FrameStream::create($this, $stream);
				}
			}
			else if ($stream['type'] == 'subtitle')
			{
				$streams[] = SubtitleStream::create($this, $stream);
			}
		}
		
		$this->setStreams(new Collection($streams));
		
		foreach ($this->getStreams(self::STREAM_AUDIO | self::STREAM_VIDEO) as $stream)
		{
			$prefix = $stream instanceof VideoStreamInterface ? 'audio_' : 'video_';
			
			foreach ($stream->toArray() as $key => $value)
			{
				if ($key != 'metadata')
				{
					$demuxing->format[$key] = $value;
				}
				
				if (in_array($key, ['codec', 'bitrate'], false))
				{
					$demuxing->format[$prefix.$key] = $value;
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
	}
	
	/**
	 * Checks is supported the encoding in format.
	 *
	 * @param FormatInterface $format
	 *
	 * @return bool
	 */
	protected function isSupportedFormat(FormatInterface $format)
	{
		return $format instanceof VideoFormatInterface;
	}
	
}
