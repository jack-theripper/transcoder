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

use Arhitector\Jumper\Exception\ExecutableNotFoundException;
use Arhitector\Jumper\TranscoderInterface;

/**
 * Class Decoder.
 *
 * @package Arhitector\Jumper\Service
 */
class Decoder
{
	
	/**
	 * @var array The options.
	 */
	protected $options;
	
	/**
	 * Decoder constructor.
	 *
	 * @param array $options
	 *
	 * @throws ExecutableNotFoundException
	 */
	public function __construct(array $options = [])
	{
		$this->setOptions($this->resolveOptions($options));
	}
	
	/**
	 * Demultiplexing.
	 *
	 * @param TranscoderInterface $media
	 *
	 * @return \stdClass
	 */
	public function demuxing(TranscoderInterface $media)
	{
		$result = new \stdClass;
		$result->format = null;
		$result->streams = [];
		
		return $result;
	}
	
	/**
	 * Sets the options value.
	 *
	 * @param array $options
	 *
	 * @return Decoder
	 */
	protected function setOptions(array $options)
	{
		$this->options = $options;
		
		return $this;
	}
	
	/**
	 * The options processing.
	 *
	 * @param array $options
	 *
	 * @return array
	 * @throws \Arhitector\Jumper\Exception\ExecutableNotFoundException
	 */
	protected function resolveOptions(array $options)
	{
		return array_merge([
			'ffprobe.path' => 'ffprobe',
			'timeout'      => 60
		], $options);
	}
	
}