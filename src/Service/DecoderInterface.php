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
namespace Arhitector\Transcoder\Service;

use Arhitector\Transcoder\TranscodeInterface;

/**
 * Interface DecoderInterface.
 *
 * @package Arhitector\Transcoder\Service
 */
interface DecoderInterface
{
	
	/**
	 * Demultiplexing.
	 *
	 * @param TranscodeInterface $media
	 *
	 * @return \stdClass
	 */
	public function demuxing(TranscodeInterface $media);
	
	/**
	 * Gets the options.
	 *
	 * @return array
	 */
	public function getOptions();
	
	/**
	 * Sets the options value.
	 *
	 * @param array $options
	 *
	 * @return $this
	 */
	public function setOptions(array $options);
	
}
