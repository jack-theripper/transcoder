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
namespace Arhitector\Jumper\Tests\Traits;

use Arhitector\Jumper\Traits\OptionsAwareTrait;
use PHPUnit_Framework_Error;

/**
 * Class OptionsAwareTraitTest.
 *
 * @package Arhitector\Jumper\Tests\Traits
 */
class OptionsAwareTraitTest extends \PHPUnit_Framework_TestCase
{
	
	public function testSuccessful()
	{
		$mock = $this->getMockForTrait(OptionsAwareTrait::class);
		$this->assertInstanceOf(get_class($mock), $mock->setOptions(['key' => 'value']));
		$this->assertEquals(['key' => 'value'], $mock->getOptions());
	}
	
	/**
	 * @dataProvider dataProviderFailure
	 */
	public function testFailure($value)
	{
		$this->expectException(get_class(new PHPUnit_Framework_Error("", 0, "", 1)));
		$mock = $this->getMockForTrait(OptionsAwareTrait::class);
		$mock->setOptions($value);
	}
	
	public function dataProviderFailure()
	{
		return [
			['string'],
		    [1234567890],
		    [new \stdClass]
		];
	}
	
}
