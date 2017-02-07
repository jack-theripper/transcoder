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
namespace Arhitector\Transcoder\Filter;

use Arhitector\Transcoder\Format\FormatInterface;
use Arhitector\Transcoder\TranscodeInterface;

/**
 * Interface FilterInterface.
 *
 * @package Arhitector\Transcoder\Filter
 */
interface FilterInterface
{
	
	/**
	 * Apply filter.
	 *
	 * @param TranscodeInterface $media
	 * @param FormatInterface    $format
	 *
	 * @return array
	 */
	public function apply(TranscodeInterface $media, FormatInterface $format);
	
}
