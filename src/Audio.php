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
use Arhitector\Transcoder\Format\AudioFormat;
use Arhitector\Transcoder\Format\AudioFormatInterface;
use Arhitector\Transcoder\Format\FormatInterface;
use Arhitector\Transcoder\Format\FrameFormatInterface;
use Arhitector\Transcoder\Stream\AudioStream;
use Arhitector\Transcoder\Stream\AudioStreamInterface;
use Arhitector\Transcoder\Stream\Collection;
use Arhitector\Transcoder\Stream\FrameStream;
use Arhitector\Transcoder\Stream\VideoStream;

/**
 * Class Audio.
 *
 * @package Arhitector\Transcoder
 */
class Audio implements AudioInterface
{
	use TranscodeTrait;
	
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
	 * @return AudioFormatInterface
	 */
	public function getFormat()
	{
		return $this->format;
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
	 * Initializing.
	 *
	 * @param \StdClass $demuxing
	 *
	 * @return void
	 */
	protected function initialize(\StdClass $demuxing)
	{
		$format = $this->findFormatClass($demuxing->format['format'], AudioFormat::class);
		
		if ( ! is_subclass_of($format, AudioFormatInterface::class))
		{
			throw new TranscoderException(sprintf('This format unsupported in the "%s" wrapper.', __CLASS__));
		}
		
		$streams = [];
		
		foreach ($demuxing->streams as $number => $stream)
		{
			$stream['type'] = isset($stream['type']) ? strtolower($stream['type']) : null;
			
			if ($stream['type'] == 'audio')
			{
				$streams[$number] = AudioStream::create($this, $stream);
			}
			else if ($stream['type'] == 'video')
			{
				if ($this instanceof AudioInterface && $this instanceof VideoInterface)
				{
					$streams[$number] = VideoStream::create($this, $stream);
				}
				else
				{
					$streams[$number] = FrameStream::create($this, $stream);
				}
			}
		}
		
		$this->setStreams(new Collection($streams));
		
		foreach ($this->getStreams(self::STREAM_AUDIO | self::STREAM_FRAME) as $stream)
		{
			$prefix = $stream instanceof AudioStreamInterface ? 'audio_' : 'video_';
			
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
		return $format instanceof AudioFormatInterface && ! $format instanceof FrameFormatInterface;
	}
	
}
