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

use Arhitector\Transcoder\Format\FormatInterface;
use Arhitector\Transcoder\TranscodeInterface;

/**
 * Interface EncoderInterface.
 *
 * @package Arhitector\Transcoder\Service
 */
interface EncoderInterface
{
	
	/**
	 * Constructs and returns the iterator with instances of 'Process'.
	 *
	 * @param TranscodeInterface $media  it may be a stream or media wrapper.
	 * @param FormatInterface    $format new format.
	 * @param array              $options
	 *
	 * @return \Iterator|\Symfony\Component\Process\Process[]
	 */
	public function transcoding(TranscodeInterface $media, FormatInterface $format, array $options = []);
	
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
