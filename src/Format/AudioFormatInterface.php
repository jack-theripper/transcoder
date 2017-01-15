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
namespace Arhitector\Jumper\Format;

use Arhitector\Jumper\Codec;

/**
 * Interface AudioFormatInterface.
 *
 * @package Arhitector\Jumper\Format
 */
interface AudioFormatInterface extends FormatInterface
{
	
	/**
	 * Gets the audio channels value.
	 *
	 * @return int
	 */
	public function getAudioChannels();
	
	/**
	 * Sets the channels value.
	 *
	 * @param int $channels
	 *
	 * @return AudioFormatInterface
	 * @throws \InvalidArgumentException
	 */
	public function setAudioChannels($channels);
	
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
	public function getAudioFrequency();
	
	/**
	 * Set frequency value.
	 *
	 * @param int $frequency
	 *
	 * @return AudioFormatInterface
	 * @throws \InvalidArgumentException
	 */
	public function setAudioFrequency($frequency);
	
	/**
	 * Get available codecs.
	 *
	 * @return string[]
	 */
	public function getAvailableAudioCodecs();
	
}