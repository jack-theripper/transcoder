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
use Arhitector\Jumper\Format\FormatInterface;
use Arhitector\Jumper\TranscoderInterface;
use Iterator;
use Symfony\Component\Process\Process;

/**
 * Class Encoder.
 *
 * @package Arhitector\Jumper\Service
 */
class Encoder
{
	
	/**
	 * @var array The options.
	 */
	protected $options;
	
	/**
	 * Encoder constructor.
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
	 * Constructs and returns the iterator with instances of 'Process'.
	 * If the value $media instance of 'TranscoderInterface' then it is full media or instance of 'StreamInterface'
	 * then it is stream.
	 *
	 * @param TranscoderInterface $media  it may be a stream or media wrapper.
	 * @param FormatInterface     $format new format.
	 *
	 * @return Iterator|Process[]  returns the instances of 'Process'.
	 */
	public function transcoding(TranscoderInterface $media, FormatInterface $format)
	{
		// TODO
		return [];
	}
	
	/**
	 * Sets the options value.
	 *
	 * @param array $options
	 *
	 * @return Encoder
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
			'ffmpeg.path'    => 'ffmpeg',
			'ffmpeg.threads' => 0,
			'timeout'        => 60
		], $options);
	}
	
}