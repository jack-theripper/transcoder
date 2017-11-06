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
namespace Arhitector\Transcoder\Tests;

use Arhitector\Transcoder\TimeInterval;

/**
 * Class TimeIntervalTest
 *
 * @package Arhitector\Transcoder\Tests
 */
class TimeIntervalTest extends \PHPUnit_Framework_TestCase
{

	public function testGettersSuccessful()
	{
		$timeInterval = new TimeInterval(120);
		
		$this->assertEquals(0, $timeInterval->getSeconds());
		$this->assertEquals(120, $timeInterval->toSeconds());
	}
	
}
