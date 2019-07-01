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
namespace Arhitector\Transcoder\Filter;

use Arhitector\Transcoder\Format\FormatInterface;
use Arhitector\Transcoder\TranscodeInterface;

/**
 * Delay one or more audio channels.
 *
 * @package Arhitector\Transcoder\Filter
 */
class AudioDelay implements AudioFilterInterface
{
	
	/**
	 * @var array The delays audio channels.
	 */
	protected $delays = [];
	
	/**
	 * AudioDelay constructor.
	 *
	 * @param string[] ...$delays List of delays in milliseconds for each channel.
	 *
	 * <code>
	 * // Delay first channel by 1.5 seconds, the third channel by 0.5 seconds and leave the second channel.
	 * $filter = new AudioDelay(1500, 0, 500);
	 *
	 * // or
	 *
	 * $filter = new AudioDelay([
	 *      1500,
	 *      0,
	 *      500
	 * ]);
	 * </code>
	 */
	public function __construct(...$delays)
	{
		if (isset($delays[0]) && is_array($delays[0]))
		{
			$delays = $delays[0];
		}
		
		$this->setDelays($delays);
	}
	
	/**
	 * Apply filter.
	 *
	 * @param TranscodeInterface $media
	 * @param FormatInterface    $format
	 *
	 * @return array
	 */
	public function apply(TranscodeInterface $media, FormatInterface $format)
	{
		return [
			'filter:a' => [
				'adelay' => sprintf('delays=%s', implode('|', $this->getDelays()))
			]
		];
	}
	
	/**
	 * Get the delays values.
	 *
	 * @return array
	 */
	public function getDelays()
	{
		return $this->delays;
	}
	
	/**
	 * Set list of delays in milliseconds for each channel.
	 *
	 * @param array $delays
	 *
	 * <code>
	 * // Delay first channel by 1.5 seconds, the third channel by 0.5 seconds and leave the second channel.
	 * $filter->setDelays([
	 *      1500,
	 *      0,
	 *      500
	 * ]);
	 * </code>
	 * @return AudioDelay
	 */
	public function setDelays(array $delays)
	{
		$this->delays = array_filter($delays, 'is_scalar');
		
		return $this;
	}
	
}
