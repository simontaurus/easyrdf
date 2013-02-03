<?php

/**
 * EasyRdf
 *
 * LICENSE
 *
 * Copyright (c) 2013 Nicholas J Humfrey.  All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 * 3. The name of the author 'Nicholas J Humfrey" may be used to endorse or
 *    promote products derived from this software without specific prior
 *    written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    EasyRdf
 * @copyright  Copyright (c) 2013 Nicholas J Humfrey
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @version    $Id$
 */

require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'TestHelper.php';

class EasyRdf_ContainerTest extends EasyRdf_TestCase
{
    public function setUp()
    {
        $this->graph = new EasyRdf_Graph();
        EasyRdf_Namespace::set('ex', 'http://example.org/');
    }

    public function tearDown()
    {
        EasyRdf_Namespace::delete('ex');
    }

    public function testParseSeq()
    {
        $count = $this->graph->parse(
            readFixture('rdf-seq.rdf'),
            'rdfxml',
            'http://www.w3.org/TR/REC-rdf-syntax/'
        );

        $favourites = $this->graph->resource('ex:favourite-fruit');
        $this->assertSame('rdf:Seq', $favourites->type());
        $this->assertClass('EasyRdf_Container', $favourites);

        $this->assertSame(true, $favourites->valid());
        $this->assertSame(1, $favourites->key());
        $this->assertStringEquals('http://example.org/banana', $favourites->current());

        $favourites->next();

        $this->assertSame(true, $favourites->valid());
        $this->assertSame(2, $favourites->key());
        $this->assertStringEquals('http://example.org/apple', $favourites->current());

        $favourites->next();

        $this->assertSame(true, $favourites->valid());
        $this->assertSame(3, $favourites->key());
        $this->assertStringEquals('http://example.org/pear', $favourites->current());

        $favourites->next();

        $this->assertSame(true, $favourites->valid());
        $this->assertSame(4, $favourites->key());
        $this->assertStringEquals('http://example.org/pear', $favourites->current());

        $favourites->next();

        $this->assertSame(false, $favourites->valid());

        $favourites->rewind();

        $this->assertSame(true, $favourites->valid());
        $this->assertSame(1, $favourites->key());
        $this->assertStringEquals('http://example.org/banana', $favourites->current());
    }

    public function testIterator()
    {
        $count = $this->graph->parse(
            readFixture('rdf-seq.rdf'),
            'rdfxml',
            'http://www.w3.org/TR/REC-rdf-syntax/'
        );

        $favourites = $this->graph->resource('ex:favourite-fruit');
        $this->assertSame('rdf:Seq', $favourites->type());
        $this->assertClass('EasyRdf_Container', $favourites);
        
        $list = array();
        foreach ($favourites as $fruit) {
            $list[] = $fruit->getUri();
        }

        $this->assertEquals(
            array(
                'http://example.org/banana',
                'http://example.org/apple',
                'http://example.org/pear',
                'http://example.org/pear'
            ),
            $list
        );
    }
}