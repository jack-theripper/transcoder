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
namespace Arhitector\Transcoder\Stream;

use Arhitector\Transcoder\Codec;
use Arhitector\Transcoder\Format\FormatInterface;
use Arhitector\Transcoder\TimeInterval;
use Arhitector\Transcoder\TranscodeInterface;

/**
 * Interface StreamInterface.
 *
 * @package Arhitector\Transcoder\Stream
 */
interface StreamInterface
{
	
	/**
	 * @var int Type of audio stream.
	 */
	const T_AUDIO = 1;
	
	/**
	 * @var int Type of frame stream.
	 */
	const T_FRAME = 2;
	
	/**
	 * @var int Type of video stream.
	 */
	const T_VIDEO = 4;
	
	/**
	 * @var int Type of subtitle stream.
	 */
	const T_SUBTITLE = 8;
	
	/**
	 * Returns a new format instance.
	 *
	 * @param TranscodeInterface $media
	 * @param array              $options
	 *
	 * <code>
	 * array (size=8)
	 *  'channels' => int 2
	 *  'frequency' => int 44100
	 *  'codec' => object(Arhitector\Transcoder\Codec)[13]
	 *      protected 'codec' => string 'mp3' (length=3)
	 *      protected 'name' => string 'MP3 (MPEG audio layer 3)' (length=24)
	 *  'index' => int 1
	 *  'profile' => string 'base' (length=4)
	 *  'bitrate' => int 320000
	 *  'start_time' => float 0.025057
	 *  'duration' => float 208.53551
	 * </code>
	 *
	 * @return static
	 * @throws \InvalidArgumentException
	 */
	public static function create(TranscodeInterface $media, array $options = []);
	
	/**
	 * Get the full path to the file.
	 *
	 * @return string
	 */
	public function getFilePath();
	
	/**
	 * Get Codec instance or NULL.
	 *
	 * @return Codec|null
	 */
	public function getCodec();
	
	/**
	 * Set a new Codec instance.
	 *
	 * @param Codec $codec
	 *
	 * @return StreamInterface
	 */
	public function setCodec(Codec $codec);
	
	/**
	 * Get stream index value.
	 *
	 * @return int
	 */
	public function getIndex();
	
	/**
	 * Set a new order.
	 *
	 * @param int $position
	 *
	 * @return StreamInterface
	 */
	public function setIndex($position);
	
	/**
	 * Get profile value.
	 *
	 * @return string
	 */
	public function getProfile();
	
	/**
	 * Get bit rate value.
	 *
	 * @return int
	 */
	public function getBitrate();
	
	/**
	 * Get stream start time.
	 *
	 * @return float
	 */
	public function getStartTime();
	
	/**
	 * Get duration value.
	 *
	 * @return TimeInterval
	 */
	public function getDuration();
	
	/**
	 * Stream save.
	 *
	 * @param FormatInterface $format
	 * @param string          $filePath
	 * @param bool            $overwrite
	 *
	 * @return bool
	 */
	public function save(FormatInterface $format, $filePath, $overwrite = true);
	
	/**
	 * Get array of values.
	 *
	 * @return array
	 */
	public function toArray();
	
}
