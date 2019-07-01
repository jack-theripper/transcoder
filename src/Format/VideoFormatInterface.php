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

/**
 * Interface VideoFormatInterface.
 *
 * @package Arhitector\Transcoder\Format
 */
interface VideoFormatInterface extends FrameFormatInterface, AudioFormatInterface
{
	
	/**
	 * Get the video bitrate value.
	 *
	 * @return int
	 */
	public function getVideoBitrate();
	
	/**
	 * Set the bitrate value.
	 *
	 * @param int $bitrate
	 *
	 * @return VideoFormatInterface
	 * @throws \InvalidArgumentException
	 */
	public function setVideoBitrate($bitrate);
	
	/**
	 * Sets the number of passes.
	 *
	 * @param int $passes
	 *
	 * @return VideoFormatInterface
	 */
	public function setPasses($passes);
	
	/**
	 * Get the frame rate value.
	 *
	 * @return float
	 */
	public function getFrameRate();
	
	/**
	 * Set the frame rate value.
	 *
	 * @param float $frameRate
	 *
	 * @return VideoFormatInterface
	 */
	public function setFrameRate($frameRate);
	
}
