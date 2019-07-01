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
namespace Arhitector\Transcoder\Stream;

/**
 * Interface VideoStreamInterface.
 *
 * @package Arhitector\Transcoder\Stream
 */
interface VideoStreamInterface extends FrameStreamInterface
{
	
	/**
	 * Get frame rate value.
	 *
	 * @return float
	 */
	public function getFrameRate();
	
}
