<?php

namespace Youthweb\BBCodeParser\Tests\Unit\Config;

use Youthweb\BBCodeParser\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider SetterDataProvider
	 */
	public function testSetter($key, $default, $value)
	{
		$validation = $this->getMockBuilder('Youthweb\BBCodeParser\ValidationInterface')
			->getMock();

		$config = new Config($validation);

		$this->assertSame($default, $config->get($key));

		$this->assertSame($config, $config->set($key, $value));

		$this->assertSame($value, $config->get($key));
	}

	/**
	 * @SetterDataProvider
	 */
	public function SetterDataProvider()
	{
		return [
			[
				'foobar',
				null,
				'baz',
			],
			[
				'foo.bar',
				null,
				'baz',
			],
			[
				'foo.0.bar',
				null,
				'baz',
			],
		];
	}

	/**
	 * @test
	 */
	public function testGetter()
	{
		$validation = $this->getMockBuilder('Youthweb\BBCodeParser\ValidationInterface')
			->getMock();

		$config = new Config($validation);

		$this->assertSame('default', $config->get('foo.bar', 'default'));
	}
}
