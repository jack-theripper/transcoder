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
namespace Arhitector\Jumper;

use Arhitector\Jumper\Exception\TranscoderException;
use Arhitector\Jumper\Format\FormatInterface;
use Arhitector\Jumper\Stream\Collection;
use Arhitector\Jumper\Stream\StreamInterface;

/**
 * Interface TranscoderInterface.
 *
 * @package Arhitector\Jumper
 */
interface TranscoderInterface
{
	
	/**
	 * Get the full path to the file.
	 *
	 * @return string
	 */
	public function getFilePath();
	
	/**
	 * Get duration value.
	 *
	 * @return float
	 */
	public function getDuration();
	
	/**
	 * Get current format.
	 *
	 * @return FormatInterface
	 * @throws TranscoderException
	 */
	public function getFormat();
	
	/**
	 * Get a list of streams.
	 *
	 * @return Collection|StreamInterface[]
	 * @throws TranscoderException
	 */
	public function getStreams();
	
}