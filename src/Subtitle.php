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
use Arhitector\Transcoder\Filter\FilterInterface;
use Arhitector\Transcoder\Format\FormatInterface;
use Arhitector\Transcoder\Format\SubtitleFormat;
use Arhitector\Transcoder\Format\SubtitleFormatInterface;
use Arhitector\Transcoder\Stream\Collection;
use Arhitector\Transcoder\Stream\SubtitleStream;

/**
 * Class Subtitle.
 *
 * @package Arhitector\Transcoder
 */
class Subtitle implements SubtitleInterface
{
	use TranscodeTrait;
	
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
		throw new TranscoderException(sprintf('The "%s" wrapper unsuppored filers.', __CLASS__));
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
		/** @var SubtitleFormatInterface $format */
		$format = $this->findFormatClass($demuxing->format['format'], SubtitleFormat::class);
		
		if ( ! is_subclass_of($format, SubtitleFormatInterface::class))
		{
			throw new TranscoderException(sprintf('This format unsupported in the "%s" wrapper.', __CLASS__));
		}
		
		$streams =  new Collection();
		
		foreach ($demuxing->streams as $number => $stream)
		{
			if (isset($stream['type']) && strtolower($stream['type']) == 'subtitle')
			{
				$streams[$number] = SubtitleStream::create($this, $stream);
			}
		}
		
		$this->setStreams($streams);
		
		$this->setFormat($format::fromArray(array_filter($demuxing->format, function ($value) {
			return $value !== null;
		})));
	}
	
	/**
	 * It supports the type of media.
	 *
	 * @return bool
	 */
	protected function isSupportedFileType()
	{
		return ! (stripos($this->getMimeType(), 'text/plain') !== 0);
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
		return $format instanceof SubtitleFormatInterface;
	}
	
}
