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
namespace Arhitector\Jumper\Service;

use Arhitector\Jumper\Format\FormatInterface;
use Arhitector\Jumper\TranscoderInterface;

/**
 * Interface EncoderInterface.
 *
 * @package Arhitector\Jumper\Service
 */
interface EncoderInterface
{
	
	/**
	 * Constructs and returns the iterator with instances of 'Process'.
	 *
	 * @param TranscoderInterface $media  it may be a stream or media wrapper.
	 * @param FormatInterface     $format new format.
	 * @param array               $options
	 *
	 * @return \Iterator|\Symfony\Component\Process\Process[]
	 */
	public function transcoding(TranscoderInterface $media, FormatInterface $format, array $options = []);
	
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
