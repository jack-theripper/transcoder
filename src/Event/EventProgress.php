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
namespace Arhitector\Transcoder\Event;

use Arhitector\Transcoder\Format\FormatInterface;
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
	 * @var FormatInterface
	 */
	protected $format;
	
	/**
	 * EventProgress constructor.
	 *
	 * @param string          $pass
	 * @param FormatInterface $format
	 */
	public function __construct($pass, FormatInterface $format)
	{
		$this->setCurrentPass((int) $pass);
		$this->setDuration($format->getDuration()->toSeconds());
		$this->setTotalPass($format->getPasses());
		$this->format = $format;
		
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
		if ($this->duration == 0)
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
	 * Emit an event.
	 *
	 * @param string $type
	 * @param string $data
	 *
	 * @return mixed
	 */
	public function __invoke($type, $data)
	{
		if (preg_match('/size=(.*?) time=(.*?) /', $data, $matches))
		{
			$matches[2] = array_reverse(explode(':', $matches[2]));
			$duration = (float) array_shift($matches[2]);
			
			foreach ($matches[2] as $key => $value)
			{
				$duration += (int) $value * (60 ** ($key + 1));
			}
			
			$this->setCurrentSize((int) trim($matches[1]));
			$this->setCurrentTime($duration);
			
			$this->format->emit($this);
		}
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
