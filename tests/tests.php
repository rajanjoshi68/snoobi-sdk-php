<?php

use GuzzleHttp\Exception\ParseException;
use GuzzleHttp\Message\Request;

class SnoobiTestCase extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->client = new Snoobi\Client();
    }

    public function testDoQueryReturnsResponseBodyIfJsonParseFails()
    {
        $response = $this->getMockBuilder('GuzzleHttp\Message\Response')
            ->disableOriginalConstructor()
            ->setMethods(array('getStatusCode', 'json', 'getBody'))
            ->getMock();

        $response->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(200));

        $response->expects($this->once())
            ->method('json')
            ->will($this->throwException(new ParseException()));

        $response->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue('body'));

        $client = $this->getMockBuilder('GuzzleHttp\Client')
            ->disableOriginalConstructor()
            ->setMethods(array('createRequest', 'send'))
            ->getMock();

        $client->expects($this->once())
            ->method('createRequest')
            ->will($this->returnValue(new Request(null, null)));

        $client->expects($this->once())
            ->method('send')
            ->will($this->returnValue($response));

        $property = new \ReflectionProperty($this->client, 'client');
        $property->setAccessible(true);
        $property->setValue($this->client, $client);

        $this->assertEquals('body', $this->client->get('test'));
    }

    public function testDoQueryReturnsResponseBodyJson()
    {
        $response = $this->getMockBuilder('GuzzleHttp\Message\Response')
            ->disableOriginalConstructor()
            ->setMethods(array('getStatusCode', 'json'))
            ->getMock();

        $response->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(200));

        $response->expects($this->once())
            ->method('json')
            ->will($this->returnValue(['json']));

        $client = $this->getMockBuilder('GuzzleHttp\Client')
            ->disableOriginalConstructor()
            ->setMethods(array('createRequest', 'send'))
            ->getMock();

        $client->expects($this->once())
            ->method('createRequest')
            ->will($this->returnValue(new Request(null, null)));

        $client->expects($this->once())
            ->method('send')
            ->will($this->returnValue($response));

        $property = new \ReflectionProperty($this->client, 'client');
        $property->setAccessible(true);
        $property->setValue($this->client, $client);

        $this->assertEquals(['json'], $this->client->get('test'));
    }

    public function testDoQueryThrowsAnExceptionWithInvalidResponseCode()
    {
        $response = $this->getMockBuilder('GuzzleHttp\Message\Response')
            ->disableOriginalConstructor()
            ->setMethods(array('getStatusCode', 'json', 'getBody'))
            ->getMock();

        $response->expects($this->exactly(2))
            ->method('getStatusCode')
            ->will($this->returnValue(300));

        $response->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue('body'));

        $client = $this->getMockBuilder('GuzzleHttp\Client')
            ->disableOriginalConstructor()
            ->setMethods(array('createRequest', 'send'))
            ->getMock();

        $client->expects($this->once())
            ->method('createRequest')
            ->will($this->returnValue(new Request(null, null)));

        $client->expects($this->once())
            ->method('send')
            ->will($this->returnValue($response));

        $property = new \ReflectionProperty($this->client, 'client');
        $property->setAccessible(true);
        $property->setValue($this->client, $client);

        try {
            $result = $this->client->get('health');
            $this->assertTrue(false, 'Expected Snoobi\SnoobiApiException');
        } catch(Snoobi\SnoobiApiException $e) {
            $this->assertEquals('body', $e->getMessage());
            $this->assertEquals(300, $e->getCode());
        } catch(\Exception $e)
        {
            $this->assertTrue(false, 'Expected Snoobi\SnoobiApiException');
        }
    }
}
