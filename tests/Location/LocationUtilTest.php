<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\UtilsBundle\Tests\Location;

use Contao\Config;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\DataContainer;
use Contao\TestCase\ContaoTestCase;
use HeimrichHannot\UtilsBundle\Location\LocationUtil;
use HeimrichHannot\UtilsBundle\String\StringUtil;
use HeimrichHannot\UtilsBundle\Tests\ModelMockTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LocationUtilTest extends ContaoTestCase
{
    use ModelMockTrait;

    /**
     * Tests the object instantiation.
     */
    public function testCanBeInstantiated()
    {
        $instance = new LocationUtil($this->getContainerMock());
        $this->assertInstanceOf(LocationUtil::class, $instance);
    }

    public function testComputeCoordinatesByArray()
    {
        $locationUtil = new LocationUtil($this->getContainerMock());

        $result = $locationUtil->computeCoordinatesByArray([
            'street' => 'Bayrische Straße 18',
            'postal' => '01067',
            'city' => 'Dresden',
            'country' => '',
        ]);
        $this->assertSame(['lat' => 12, 'lng' => 22], $result);

        $result = $locationUtil->computeCoordinatesByArray([
            'street' => '',
            'postal' => '',
            'city' => 'Dresden',
            'country' => '',
        ]);
        $this->assertSame(['lat' => 13, 'lng' => 23], $result);

        $result = $locationUtil->computeCoordinatesByArray([
            'street' => 'Martin-Luther-Ring 12',
            'postal' => '',
            'city' => 'Leipzig',
            'country' => '',
        ]);
        $this->assertSame(['lat' => 14, 'lng' => 24], $result);

        $result = $locationUtil->computeCoordinatesByArray([
            'something' => 'Error',
        ]);
        $this->assertFalse($result);
    }

    public function testComputeCoordinatesByString()
    {
        $locationUtil = new LocationUtil($this->getContainerMock());

        Config::set('utilsGoogleApiKey', 'baz');
        $result = $locationUtil->computeCoordinatesByString('Bayrische Straße 18 01067 Dresden');
        $this->assertSame(['lat' => 12, 'lng' => 22], $result);
        $result = $locationUtil->computeCoordinatesByString('Dresden');
        $this->assertSame(['lat' => 13, 'lng' => 23], $result);
        $result = $locationUtil->computeCoordinatesByString('Martin-Luther-Ring 12  Leipzig');
        $this->assertSame(['lat' => 14, 'lng' => 24], $result);
        $result = $locationUtil->computeCoordinatesByString('Error');
        $this->assertFalse($result);
        $result = $locationUtil->computeCoordinatesByString('');
        $this->assertFalse($result);
    }

    public function testComputeCoordinatesInSaveCallback()
    {
        $locationUtil = new LocationUtil($this->getContainerMock());

        $activeRecordMock = new \stdClass();

        $activeRecordMock->street = 'Bayrische Straße 18';
        $activeRecordMock->postal = '01067';
        $activeRecordMock->city = 'Dresden';

        $dataContainerMock = $this->mockModelObject(DataContainer::class, ['activeRecord' => $activeRecordMock]);
        $result = $locationUtil->computeCoordinatesInSaveCallback('', $dataContainerMock);
        $this->assertSame('12,22', $result);

        $activeRecordMock->street = '';
        $activeRecordMock->postal = '';
        $activeRecordMock->city = 'Dresden';

        $dataContainerMock = $this->mockModelObject(DataContainer::class, ['activeRecord' => $activeRecordMock]);
        $result = $locationUtil->computeCoordinatesInSaveCallback('', $dataContainerMock);
        $this->assertSame('13,23', $result);

        $activeRecordMock->street = 'Martin-Luther-Ring 12';
        $activeRecordMock->postal = '';
        $activeRecordMock->city = 'Leipzig';

        $dataContainerMock = $this->mockModelObject(DataContainer::class, ['activeRecord' => $activeRecordMock]);
        $result = $locationUtil->computeCoordinatesInSaveCallback('', $dataContainerMock);
        $this->assertSame('14,24', $result);

        $activeRecordMock->street = '';
        $activeRecordMock->postal = '';
        $activeRecordMock->city = '';

        $dataContainerMock = $this->mockModelObject(DataContainer::class, ['activeRecord' => $activeRecordMock]);
        $result = $locationUtil->computeCoordinatesInSaveCallback('', $dataContainerMock);
        $this->assertSame('', $result);

        $activeRecordMock->street = 'ArrayError';

        $dataContainerMock = $this->mockModelObject(DataContainer::class, ['activeRecord' => $activeRecordMock]);
        $result = $locationUtil->computeCoordinatesInSaveCallback('', $dataContainerMock);
        $this->assertSame('', $result);
    }

    /**
     * @param ContainerBuilder|null $container
     * @param ContaoFramework       $framework
     *
     * @return ContainerBuilder|ContainerInterface
     */
    protected function getContainerMock(ContainerBuilder $container = null, $framework = null)
    {
        if (!$container) {
            $container = $this->mockContainer();
        }

        if (!$framework) {
            $framework = $this->mockContaoFramework();
        }
        $container->set('contao.framework', $framework);

        try {
            /** @noinspection PhpParamsInspection */
            $stringUtil = new StringUtil($container->get('contao.framework'));
        } catch (\Exception $e) {
            $this->fail('Could net get service from container. Message: '.$e->getMessage());
        }
        $container->set('huh.utils.string', $stringUtil);

        $urlUtilMock = $this->mockAdapter([
            'addQueryString',
        ]);
        $urlUtilMock->method('addQueryString')->willReturnCallback(
            function ($query, $url = null) {
                return $url.'&'.$query;
            }
        );
        $container->set('huh.utils.url', $urlUtilMock);

        $curlUtilMock = $this->mockAdapter([
            'request',
        ]);
        $curlUtilMock->method('request')->willReturnCallback(
            function (string $url, array $requestHeaders = [], $returnResponseHeaders = false) {
                parse_str(parse_url($url)['query'], $query);
                $address = urlencode($query['address']);

                switch ($address) {
                    case 'Bayrische+Stra%C3%9Fe+18+01067+Dresden':
                        $result = json_encode(['results' => [0 => ['geometry' => ['location' => ['lat' => 12, 'lng' => 22]]]]]);

                        return $result;

                    case 'Dresden':
                        $result = json_encode(['results' => [0 => ['geometry' => ['location' => ['lat' => 13, 'lng' => 23]]]]]);

                        return $result;

                    case 'Martin-Luther-Ring+12++Leipzig':
                        $result = json_encode(['results' => [0 => ['geometry' => ['location' => ['lat' => 14, 'lng' => 24]]]]]);

                        return $result;

                    case 'Error':
                        $result = json_encode(['error_message' => 'Erromessage']);

                        return $result;

                    case 'ArrayError':
                        return false;

                    case '':
                        return false;

                    default:
                        return null;
                }
            }
        );
        $container->set('huh.utils.request.curl', $curlUtilMock);

        return $container;
    }
}
