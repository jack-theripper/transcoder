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

/**
 * Interface ServiceFactoryInterface.
 *
 * @package Arhitector\Transcoder\Service
 */
interface ServiceFactoryInterface
{
	
	/**
	 * @var string The path to the binary file 'ffmpeg'.
	 */
	const OPTION_FFMPEG_PATH = 'ffmpeg.path';
	
	/**
	 * @var string The option value alias for `ffmpeg.path`.
	 */
	const OPTION_FFMPEG_THREADS = 'ffmpeg.threads';
	
	/**
	 * @var string The option value alias for `ffprobe.path`.
	 */
	const OPTION_FFPROBE_PATH = 'ffprobe.path';
	
	/**
	 * @var string The option value alias for `timeout`.
	 */
	const OPTION_USE_TIMEOUT = 'timeout';
	
	/**
	 * @var string The option value alias for `use_queue`.
	 */
	const OPTION_USE_QUEUE = 'use_queue';
	
	/**
	 * @var string Check for codecs.
	 */
	const OPTION_TEST_CODECS = 'test_codecs';
	
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
	
	/**
	 * Get the decoder instance.
	 *
	 * @param array $options
	 *
	 * @return DecoderInterface
	 */
	public function getDecoderService(array $options = []);
	
	/**
	 * Get the encoder instance.
	 *
	 * @param array $options
	 *
	 * @return EncoderInterface
	 */
	public function getEncoderService(array $options = []);
	
}
