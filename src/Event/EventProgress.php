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
namespace Arhitector\Transcoder\Event;

use League\Event\Event;
use League\Event\EventInterface;

/**
 * Class EventProgress.
 *
 * @package Arhitector\Transcoder\Event
 */
class EventProgress extends Event implements EventInterface
{
	
	/**
	 * @var int Total pass value.
	 */
	protected $totalPass;
	
	/**
	 * @var float Duration value.
	 */
	protected $duration = 0.0;
	
	/**
	 * @var int Current pass.
	 */
	protected $currentPass = 1;
	
	/**
	 * @var float Current time in seconds.
	 */
	protected $time = 0.0;
	
	/**
	 * @var int Current file size.
	 */
	protected $size = 0;
	
	/**
	 * EventProgress constructor.
	 *
	 * @param float $duration
	 * @param int   $totalPass
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($duration, $totalPass = 1)
	{
		$this->setDuration($duration);
		$this->setTotalPass($totalPass);
		
		parent::__construct('progress');
	}
	
	/**
	 * Set the current pass value.
	 *
	 * @param int $currentPass
	 *
	 * @return EventProgress
	 */
	public function setCurrentPass($currentPass)
	{
		$this->currentPass = $currentPass;
		
		return $this;
	}
	
	/**
	 * Set current time value.
	 *
	 * @param float $time
	 *
	 * @return EventProgress
	 */
	public function setCurrentTime($time)
	{
		$this->time = $time;
		
		return $this;
	}
	
	/**
	 * Set current size value.
	 *
	 * @param int $size
	 *
	 * @return EventProgress
	 */
	public function setCurrentSize($size)
	{
		$this->size = $size;
		
		return $this;
	}
	
	/**
	 * Get current percent or '-1'.
	 *
	 * @return float
	 */
	public function getPercent()
	{
		if ($this->duration < 1)
		{
			return -1;
		}
		
		$percent = $this->time / $this->duration * 100 / $this->totalPass;
		
		return round(min(100, $percent + 100 / $this->totalPass * ($this->currentPass - 1)), 2);
	}
	
	/**
	 * Get remaining time.
	 *
	 * @return float
	 */
	public function getRemaining()
	{
		return $this->duration - $this->time;
	}
	
	/**
	 * Set total passes.
	 *
	 * @param int $totalPass
	 *
	 * @return EventProgress
	 * @throws \InvalidArgumentException
	 */
	protected function setTotalPass($totalPass)
	{
		if ($totalPass < 1)
		{
			throw new \InvalidArgumentException('The total passes value cannot be less than 1.');
		}
		
		$this->totalPass = (int) $totalPass;
		
		return $this;
	}
	
	/**
	 * Sets duration value.
	 *
	 * @param float $duration
	 *
	 * @return EventProgress
	 * @throws \InvalidArgumentException
	 */
	protected function setDuration($duration)
	{
		if ( ! is_numeric($duration))
		{
			throw new \InvalidArgumentException('The duration value must be a float type.');
		}
		
		$this->duration = abs($duration);
		
		return $this;
	}
	
}
