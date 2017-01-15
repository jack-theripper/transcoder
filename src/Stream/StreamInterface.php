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
namespace Arhitector\Jumper\Stream;

use Arhitector\Jumper\Codec;

/**
 * Interface StreamInterface.
 *
 * @package Arhitector\Jumper\Stream
 */
interface StreamInterface
{
	
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
	
}