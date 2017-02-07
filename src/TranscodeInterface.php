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

use Arhitector\Jumper\Exception\InvalidFilterException;
use Arhitector\Jumper\Exception\TranscoderException;
use Arhitector\Jumper\Filter\FilterInterface;
use Arhitector\Jumper\Format\FormatInterface;
use Arhitector\Jumper\Service\ServiceFactoryInterface;
use Arhitector\Jumper\Stream\Collection;
use Arhitector\Jumper\Stream\StreamInterface;

/**
 * Interface TranscoderInterface.
 *
 * @package Arhitector\Jumper
 */
interface TranscodeInterface
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
	 * @return Collection|StreamInterface[]
	 */
	public function getStreams();
	
	/**
	 * Transcoding.
	 *
	 * @param FormatInterface $format
	 * @param string          $filePath
	 * @param bool            $overwrite
	 *
	 * @return float|int Size of the new file or -1
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
	
}
