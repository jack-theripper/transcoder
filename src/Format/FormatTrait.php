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
namespace Arhitector\Transcoder\Format;

use Arhitector\Transcoder\Event\EmitterTrait;
use Arhitector\Transcoder\Preset\PresetInterface;
use Arhitector\Transcoder\TimeInterval;
use Arhitector\Transcoder\Traits\MetadataTrait;
use Arhitector\Transcoder\Traits\OptionsAwareTrait;

/**
 * Class FormatTrait.
 *
 * @package Arhitector\Transcoder\Format
 */
trait FormatTrait
{
	use EmitterTrait, MetadataTrait, OptionsAwareTrait {
		OptionsAwareTrait::setOptions as protected;
	}
	
	/**
	 * Returns a new format instance.
	 *
	 * @param array $options
	 *
	 * <code>
	 * array (size=6)
	 *  'audio_codec' => object(Arhitector\Transcoder\Codec)[13]
	 *      protected 'codec' => string 'mp3' (length=3)
	 *      protected 'name' => string '' (length=0)
	 *  'audio_bitrate' => int 256000
	 *  'channels' => int 6
	 *  'frequency' => int 44100
	 *  'duration' => int 900
	 *  'metadata' => array (size=2)
	 *      'title' => string 'Title' (length=5)
	 *      'artist' => string 'Artist name' (length=11)
	 * </code>
	 *
	 * @return static
	 */
	public static function fromArray(array $options)
	{
		$self = new static();
		
		foreach ($options as $option => $value)
		{
			$parameter = str_replace('_', '', 'set'.ucwords($option, '_'));
			
			if (method_exists($self, $parameter))
			{
				$self->{$parameter}($value);
			}
		}
		
		return $self;
	}
	
	/**
	 * @var TimeInterval Duration value.
	 */
	protected $duration;
	
	/**
	 * @var array List of extensions.
	 */
	protected $extensions = [];
	
	/**
	 * Get the duration value.
	 *
	 * @return TimeInterval
	 */
	public function getDuration()
	{
		return $this->duration;
	}
	
	/**
	 * Get the format extensions.
	 *
	 * @return array
	 */
	public function getExtensions()
	{
		return $this->extensions;
	}
	
	/**
	 * Clone format instance with a new parameters from preset.
	 *
	 * @param PresetInterface $preset
	 *
	 * @return FormatInterface
	 */
	public function withPreset(PresetInterface $preset)
	{
		// TODO
	}
	
	/**
	 * Set the duration value.
	 *
	 * @param TimeInterval|float $duration
	 *
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	protected function setDuration($duration)
	{
		if (is_numeric($duration) && $duration >= 0)
		{
			$duration = new TimeInterval($duration);
		}
		
		if ( ! $duration instanceof TimeInterval)
		{
			throw new \InvalidArgumentException('The duration value must be a positive number value.');
		}
		
		$this->duration = $duration;
		
		return $this;
	}
	
	/**
	 * Sets the extensions value.
	 *
	 * @param array $extensions
	 *
	 * @return array an array of values that have been set
	 */
	protected function setExtensions(array $extensions)
	{
		return $this->extensions = array_filter($extensions, 'is_scalar');
	}
	
}
