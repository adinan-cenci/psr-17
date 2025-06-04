<?php

namespace AdinanCenci\Psr17\Tests;

use AdinanCenci\Psr17\RequestFactory;
use AdinanCenci\Psr17\Helper\Arrays;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class ArrayHelperTest extends TestCase
{
    public function testGetAllPaths()
    {
        $array = [
            'tree' => [
                'branch' => 'fruit',
                'twig'   => 'leaves',
            ],
            'farm' => [
                'stable' => 'cow',
            ],
        ];

        $paths = Arrays::getAllPaths($array);

        $this->assertEquals($paths, [
            ['tree', 'branch'],
            ['tree', 'twig'],
            ['farm', 'stable'],
        ]);
    }

    public function testGetValueAtEndOfPath()
    {
        $array = [
            'tree' => [
                'branch' => 'fruit',
                'twig'   => 'leaves',
            ],
            'farm' => [
                'stable' => 'cow',
            ],
        ];

        $fruit = Arrays::getValueAtEndOfPath($array, ['tree', 'branch']);
        $this->assertEquals($fruit, 'fruit');

        $leaves = Arrays::getValueAtEndOfPath($array, ['tree', 'twig']);
        $this->assertEquals($leaves, 'leaves');

        $cow = Arrays::getValueAtEndOfPath($array, ['farm', 'stable']);
        $this->assertEquals($cow, 'cow');
    }

    public function testSetValueAtEndOfPath()
    {
        $society = [];
        Arrays::setValueAtEndOfPath($society, ['members', 'leadership', 'ainur'], 'Gandalf');

        $this->assertEquals($society['members']['leadership']['ainur'], 'Gandalf');
    }
}
