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
namespace Arhitector\Transcoder\Tests\Traits;

use Arhitector\Transcoder\Traits\OptionsAwareTrait;
use PHPUnit_Framework_Error;

/**
 * Class OptionsAwareTraitTest.
 *
 * @package Arhitector\Transcoder\Tests\Traits
 */
class OptionsAwareTraitTest extends \PHPUnit_Framework_TestCase
{
	
	public function testSuccessful()
	{
		/** @var OptionsAwareTrait $mock */
		$mock = $this->getMockForTrait(OptionsAwareTrait::class);
		$this->assertInstanceOf(get_class($mock), $mock->setOptions(['key' => 'value']));
		$this->assertEquals(['key' => 'value'], $mock->getOptions());
	}
	
	/**
	 * @dataProvider dataProviderFailure
	 */
	public function testFailure($value)
	{
		$this->expectException(get_class(new PHPUnit_Framework_Error('', 0, '', 1)));
		
		/** @var OptionsAwareTrait $mock */
		$mock = $this->getMockForTrait(OptionsAwareTrait::class);
		$mock->setOptions($value);
	}
	
	/**
	 * The data provider.
	 *
	 * @return array
	 */
	public function dataProviderFailure()
	{
		return [
			['string'],
		    [1234567890],
		    [new \stdClass]
		];
	}
	
}
