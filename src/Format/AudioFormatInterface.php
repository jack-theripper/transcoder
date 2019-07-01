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
namespace Arhitector\Transcoder\Format;

use Arhitector\Transcoder\Codec;

/**
 * Interface AudioFormatInterface.
 *
 * @package Arhitector\Transcoder\Format
 */
interface AudioFormatInterface extends FormatInterface
{
	
	/**
	 * Gets the audio channels value.
	 *
	 * @return int
	 */
	public function getChannels();
	
	/**
	 * Sets the channels value.
	 *
	 * @param int $channels
	 *
	 * @return AudioFormatInterface
	 * @throws \InvalidArgumentException
	 */
	public function setChannels($channels);
	
	/**
	 * Get audio codec.
	 *
	 * @return Codec
	 */
	public function getAudioCodec();
	
	/**
	 * Sets the audio codec, Should be in the available ones, otherwise an exception is thrown.
	 *
	 * @param Codec $audioCodec
	 *
	 * @return AudioFormatInterface
	 * @throws \InvalidArgumentException
	 */
	public function setAudioCodec(Codec $audioCodec);
	
	/**
	 * Get the audio bitrate value.
	 *
	 * @return int
	 */
	public function getAudioBitrate();
	
	/**
	 * Sets the audio bitrate value.
	 *
	 * @param int $bitRate
	 *
	 * @return AudioFormatInterface
	 * @throws \InvalidArgumentException
	 */
	public function setAudioBitrate($bitRate);
	
	/**
	 * Get frequency value.
	 *
	 * @return int
	 */
	public function getFrequency();
	
	/**
	 * Set frequency value.
	 *
	 * @param int $frequency
	 *
	 * @return AudioFormatInterface
	 * @throws \InvalidArgumentException
	 */
	public function setFrequency($frequency);
	
	/**
	 * Get available codecs.
	 *
	 * @return string[]
	 */
	public function getAvailableAudioCodecs();
	
}
