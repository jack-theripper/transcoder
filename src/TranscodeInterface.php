<?php
/**
 * This file is part of the arhitector/transcoder-ffmpeg library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 *
 * @license   http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2017-2019 Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 */
namespace Arhitector\Transcoder;

use Arhitector\Transcoder\Exception\InvalidFilterException;
use Arhitector\Transcoder\Filter\FilterInterface;
use Arhitector\Transcoder\Format\FormatInterface;
use Arhitector\Transcoder\Protocol\ProtocolInterface;
use Arhitector\Transcoder\Service\ServiceFactoryInterface;
use Arhitector\Transcoder\Stream\Collection;
use Arhitector\Transcoder\Stream\EnumerationInterface;
use Arhitector\Transcoder\Stream\StreamInterface;

/**
 * Interface TranscoderInterface.
 *
 * @package Arhitector\Transcoder
 */
interface TranscodeInterface extends EnumerationInterface
{
	
	/**
	 * Get the full path to the file.
	 *
	 * @return string
	 */
	public function getFilePath();
	
	/**
	 * Get duration value.
	 *
	 * @return float
	 */
	public function getDuration();
	
	/**
	 * Get current format.
	 *
	 * @return FormatInterface
	 */
	public function getFormat();
	
	/**
	 * Get a list of streams.
	 *
	 * @param int|callable $filter
	 *
	 * @return Collection|StreamInterface[]
	 */
	public function getStreams($filter = null);
	
	/**
	 * Transcoding.
	 *
	 * @param FormatInterface $format
	 * @param string          $filePath
	 * @param bool            $overwrite
	 *
	 * @return TranscodeInterface
	 */
	public function save(FormatInterface $format, $filePath, $overwrite = true);
	
	/**
	 * Add a new filter.
	 *
	 * @param FilterInterface $filter
	 * @param int             $priority range 0-99.
	 *
	 * @return TranscodeInterface
	 * @throws InvalidFilterException
	 */
	public function addFilter(FilterInterface $filter, $priority = 0);
	
	/**
	 * Reset filters.
	 *
	 * @return TranscodeInterface
	 */
	public function withoutFilters();
	
	/**
	 * Get the service instance.
	 *
	 * @return ServiceFactoryInterface
	 */
	public function getService();
	
	/**
	 * Add a new stream.
	 *
	 * @param StreamInterface $stream
	 *
	 * @return TranscodeInterface
	 */
	public function addStream(StreamInterface $stream);
	
	/**
	 * Returns the current source.
	 *
	 * @return ProtocolInterface
	 */
	public function getSource(): ProtocolInterface;
	
}
