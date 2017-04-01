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
namespace Arhitector\Transcoder\Traits;

/**
 * Class ConvertEncodingTrait.
 *
 * @package Arhitector\Transcoder\Traits
 */
trait ConvertEncodingTrait
{
	
	/**
	 * Convert character encoding.
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	protected function convertEncoding($text)
	{
		// UTF 8 for Linux and OSX
		return mb_convert_encoding($text, stripos(PHP_OS, 'WIN') === false ? 'UTF-8' : 'WINDOWS-1251');
	}
	
}
