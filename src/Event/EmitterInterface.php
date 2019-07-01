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

use League\Event\EventInterface;
use League\Event\ListenerAcceptorInterface;
use League\Event\ListenerInterface;

/**
 * Interface EmitterInterface.
 *
 * @package Arhitector\Transcoder\Event
 */
interface EmitterInterface extends ListenerAcceptorInterface
{
	
	/**
	 * Emit an event.
	 *
	 * @param string|EventInterface $event
	 *
	 * @return EventInterface
	 */
	public function emit($event);
	
}
