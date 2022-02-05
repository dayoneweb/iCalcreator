<?php
/**
 * iCalcreator, the PHP class package managing iCal (rfc2445/rfc5445) calendar information.
 *
 * This file is a part of iCalcreator.
 *
 * @author    Kjell-Inge Gustafsson, kigkonsult <ical@kigkonsult.se>
 * @copyright 2007-2022 Kjell-Inge Gustafsson, kigkonsult, All rights reserved
 * @link      https://kigkonsult.se
 * @license   Subject matter of licence is the software iCalcreator.
 *            The above copyright, link, package and version notices,
 *            this licence notice and the invariant [rfc5545] PRODID result use
 *            as implemented and invoked in iCalcreator shall be included in
 *            all copies or substantial portions of the iCalcreator.
 *
 *            iCalcreator is free software: you can redistribute it and/or modify
 *            it under the terms of the GNU Lesser General Public License as
 *            published by the Free Software Foundation, either version 3 of
 *            the License, or (at your option) any later version.
 *
 *            iCalcreator is distributed in the hope that it will be useful,
 *            but WITHOUT ANY WARRANTY; without even the implied warranty of
 *            MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *            GNU Lesser General Public License for more details.
 *
 *            You should have received a copy of the GNU Lesser General Public License
 *            along with iCalcreator. If not, see <https://www.gnu.org/licenses/>.
 */
namespace Kigkonsult\Icalcreator;

use Exception;
use Kigkonsult\Icalcreator\Util\GeoFactory;
use Kigkonsult\Icalcreator\Util\ParameterFactory;
use Kigkonsult\Icalcreator\Util\StringFactory;
use Kigkonsult\Icalcreator\Util\Util;

/**
 * class Prop1TextSingleTest,
 *
 * testing VALUE TEXT etc
 *   ATTACH, ATTENDEE, CATEGORIES, CLASS, COMMENT, CONTACT, DESCRIPTION, LOCATION, ORGANIZER,
 *   RELATED-TO, REQUEST_STATUS, RESOURCES, STATUS, SUMMARY, TRANSP, URL, X-PROP
 *   COLOR, IMAGE, CONFERENCE, NAME
 * testing GeoLocation
 * testing empty properties
 * testing parse eol-htab
 *
 * @since  2.39 - 2021-06-19
 */
class Prop1TextSingleTest extends DtBase
{
    /**
     * @var string
     */
    private static string $ERRFMT   = "Error %sin case #%s, %s <%s>->%s";

    /**
     * @var string[]
     */
    private static array $STCPAR   = [ 'X-PARAM' => 'Y-vALuE' ];

    /**
     * @var array|string[]
     */
    private static array $EOLCHARS = [ "\r\n ", "\r\n\t", PHP_EOL . " ", PHP_EOL . "\t" ];

    /**
     * miscTest1 provider, test values for TEXT (single) properties
     *
     * @return mixed[]
     */
    public function textSingleTest1Provider() : array
    {
        $dataArr = [];

        // TRANSP
        $value  = IcalInterface::OPAQUE;
        $params = self::$STCPAR;
        $dataArr[] = [
            1011,
            [
                IcalInterface::TRANSP => [ IcalInterface::VEVENT ]
            ],
            $value,
            $params,
            [
                Util::$LCvalue  => $value,
                Util::$LCparams => $params
            ],
            IcalInterface::TRANSP . ParameterFactory::createParams( $params ) . ':' . $value
        ];

        // DESCRIPTION
        $value  = 'Meeting to provide technical review for \'Phoenix\' design.\nHappy Face Conference Room. Phoenix design team MUST attend this meeting.\nRSVP to team leader.';
        $params = [
            IcalInterface::ALTREP   => 'This is an alternative representation',
            IcalInterface::LANGUAGE => 'EN'
        ] + self::$STCPAR;
        $dataArr[] = [
            1021,
            [
                IcalInterface::DESCRIPTION => [
                    IcalInterface::VEVENT,
                    IcalInterface::VTODO,
                    IcalInterface::AVAILABLE,
                    IcalInterface::VAVAILABILITY
                ]
            ],
            $value,
            $params,
            [
                Util::$LCvalue  => $value,
                Util::$LCparams => $params
            ],
            IcalInterface::DESCRIPTION .
            ParameterFactory::createParams( $params, [ IcalInterface::ALTREP, IcalInterface::LANGUAGE ] ) .
            ':' . $value
        ];

        // LOCATION
        $value  = 'Conference Room - F123, Bldg. 002';
        $params = [
            IcalInterface::ALTREP   => 'This is an alternative representation',
            IcalInterface::LANGUAGE => 'EN'
        ] + self::$STCPAR;
        $dataArr[] = [
            1031,
            [
                IcalInterface::LOCATION => [
                    IcalInterface::VEVENT,
                    IcalInterface::VTODO,
                    IcalInterface::AVAILABLE,
                    IcalInterface::VAVAILABILITY
                ]
            ],
            $value,
            $params,
            [
                Util::$LCvalue  => $value,
                Util::$LCparams => $params
            ],
            IcalInterface::LOCATION .
            ParameterFactory::createParams( $params, [ IcalInterface::ALTREP, IcalInterface::LANGUAGE ] ) .
            ':' . $value
        ];

        // SUMMARY
        $value  = 'Department Party';
        $params = [
            IcalInterface::ALTREP   => 'This is an alternative representation',
            IcalInterface::LANGUAGE => 'EN'
        ] + self::$STCPAR;
        $dataArr[] = [
            1041,
            [
                IcalInterface::SUMMARY => [
                    IcalInterface::VEVENT,
                    IcalInterface::VTODO,
                    IcalInterface::VJOURNAL,
                    IcalInterface::AVAILABLE,
                    IcalInterface::VAVAILABILITY
                ]
            ],
            $value,
            $params,
            [
                Util::$LCvalue  => $value,
                Util::$LCparams => $params
            ],
            IcalInterface::SUMMARY .
            ParameterFactory::createParams( $params, [ IcalInterface::ALTREP, IcalInterface::LANGUAGE ] ) .
            ':' . $value
        ];

        $value = '⚽ Major League Soccer on ESPN+';
        $params = [
                IcalInterface::ALTREP   => 'This is an alternative representation',
                IcalInterface::LANGUAGE => 'EN'
            ] + self::$STCPAR;
        $dataArr[] = [ // testing utf8 char
            1042,
            [
                IcalInterface::SUMMARY => [
                    IcalInterface::VEVENT,
                    IcalInterface::VTODO,
                    IcalInterface::VJOURNAL,
                    IcalInterface::AVAILABLE,
                    IcalInterface::VAVAILABILITY
                ]
            ],
            $value,
            $params,
            [
                Util::$LCvalue  => $value,
                Util::$LCparams => $params
            ],
            IcalInterface::SUMMARY .
            ParameterFactory::createParams( $params, [ IcalInterface::ALTREP, IcalInterface::LANGUAGE ] ) .
            ':' . $value
        ];

        // SOURCE
        $value  = 'http://example.com/pub/calendars/jsmith/mytime.ics';
        $params = []  + self::$STCPAR;
        $dataArr[] = [
            1051,
            [
                IcalInterface::SOURCE => [ IcalInterface::VEVENT, IcalInterface::VTODO, IcalInterface::VJOURNAL, IcalInterface::VFREEBUSY ]
            ],
            $value,
            $params,
            [
                Util::$LCvalue  => $value,
                Util::$LCparams => $params
            ],
            IcalInterface::SOURCE . ParameterFactory::createParams( $params ) . ':' . $value
        ];

        // URL 1
        $value1  = '%3C01020175ae0fa363-b7ebfe82-02d0-420a-a8d9-331e43fa1867-000000@eu-west-1.amazonses.com%3E';
        $value2  = '01020175ae0fa363-b7ebfe82-02d0-420a-a8d9-331e43fa1867-000000@eu-west-1.amazonses.com';
        $params1 = [  IcalInterface::VALUE => 'URI' ]  + self::$STCPAR;
        $params2 = self::$STCPAR;
        $dataArr[] = [
            1061,
            [
                IcalInterface::URL => [
                    IcalInterface::VEVENT,
                    IcalInterface::VTODO,
                    IcalInterface::VJOURNAL,
                    IcalInterface::VFREEBUSY,
                    IcalInterface::VAVAILABILITY
                ]
            ],
            $value1,
            $params1,
            [
                Util::$LCvalue  => $value2,
                Util::$LCparams => $params2
            ],
            IcalInterface::URL . ParameterFactory::createParams( $params2 ) . ':' . $value2
        ];

        // URL 2
        $value1  = 'https://www.masked.de/account/subscription/delivery/8878/%3Fweek=2021-W03';
        $value2  = 'https://www.masked.de/account/subscription/delivery/8878/%3Fweek=2021-W03';
        $params1 = [  IcalInterface::VALUE => IcalInterface::URI ]  + self::$STCPAR;
        $params2 = self::$STCPAR;
        $dataArr[] = [
            1062,
            [
                IcalInterface::URL => [
                    IcalInterface::VEVENT,
                    IcalInterface::VTODO,
                    IcalInterface::VJOURNAL,
                    IcalInterface::VFREEBUSY,
                    IcalInterface::VAVAILABILITY
                ]
            ],
            $value1,
            $params1,
            [
                Util::$LCvalue  => $value2,
                Util::$LCparams => $params2
            ],
            IcalInterface::URL . ParameterFactory::createParams( $params2 ) . ':' . $value2
        ];


        // URL 4
        $value1  = 'message://https://www.masked.de/account/subscription/delivery/8878/%3Fweek=2021-W03';
        $value2  = 'message://https://www.masked.de/account/subscription/delivery/8878/%3Fweek=2021-W03';
        $params1 = self::$STCPAR + [  IcalInterface::VALUE => IcalInterface::URI ];
        $params2 = self::$STCPAR;
        $dataArr[] = [
            1064,
            [
                IcalInterface::URL => [
                    IcalInterface::VEVENT,
                    IcalInterface::VTODO,
                    IcalInterface::VJOURNAL,
                    IcalInterface::VFREEBUSY,
                    IcalInterface::VAVAILABILITY
                ]
            ],
            $value1,
            $params1,
            [
                Util::$LCvalue  => $value2,
                Util::$LCparams => $params2
            ],
            IcalInterface::URL . ParameterFactory::createParams( $params2 ) . ':' . $value2
        ];

        // URL 5
        $value1  = 'message://%3C1714214488.13907.1453128266311.JavaMail.tomcat%40web-pdfe-f02%3E?c=1453128266&k1=ticket&k2=1797815930&k3=2016-07-20';
        $value2  = 'message://1714214488.13907.1453128266311.JavaMail.tomcat@web-pdfe-f02?c=1453128266&k1=ticket&k2=1797815930&k3=2016-07-20';
        $params1 = self::$STCPAR + [  IcalInterface::VALUE => IcalInterface::URI ];
        $params2 = self::$STCPAR;
        $dataArr[] = [
            1065,
            [
                IcalInterface::URL => [
                    IcalInterface::VEVENT,
                    IcalInterface::VTODO,
                    IcalInterface::VJOURNAL,
                    IcalInterface::VFREEBUSY,
                    IcalInterface::VAVAILABILITY
                ]
            ],
            $value1,
            $params1,
            [
                Util::$LCvalue  => $value2,
                Util::$LCparams => $params2
            ],
            IcalInterface::URL . ParameterFactory::createParams( $params2 ) . ':' . $value2
        ];

        // URL 6
        $value1  = 'message://%3C1714214488.13907.1453128266311.JavaMail.tomcat%40web-pdfe-f02%3E?c=1453128266&k1=ticket&k2=1797815930&k3=2016-07-20';
        $value2  = 'message://1714214488.13907.1453128266311.JavaMail.tomcat@web-pdfe-f02?c=1453128266&k1=ticket&k2=1797815930&k3=2016-07-20';
        $params1 = self::$STCPAR + [  strtolower( IcalInterface::VALUE ) => strtolower( IcalInterface::URI ) ];
        $params2 = self::$STCPAR;
        $dataArr[] = [
            1066,
            [
                IcalInterface::URL => [
                    IcalInterface::VEVENT,
                    IcalInterface::VTODO,
                    IcalInterface::VJOURNAL,
                    IcalInterface::VFREEBUSY,
                    IcalInterface::VAVAILABILITY
                ]
            ],
            $value1,
            $params1,
            [
                Util::$LCvalue  => $value2,
                Util::$LCparams => $params2
            ],
            IcalInterface::URL .
            ParameterFactory::createParams( $params2 ) . ':' . $value2
        ];

        // ORGANIZER
        $value  = 'MAILTO:ildoit1071@example.com';
        $params = [
                IcalInterface::CN             => 'John Doe',
                IcalInterface::DIR            => 'ldap://example.com:6666/o=ABC%20Industries,c=US???(cn=Jim%20Dolittle)',
                IcalInterface::SENT_BY        => 'MAILTO:boss1071@example.com',
                IcalInterface::LANGUAGE       => 'EN'
            ] + self::$STCPAR;
        $getValue  = [
            Util::$LCvalue  => $value,
            Util::$LCparams => $params
        ];
        $dataArr[] = [
            1071,
            [
                IcalInterface::ORGANIZER => [ IcalInterface::VEVENT, IcalInterface::VTODO, IcalInterface::VJOURNAL ]
            ],
            $value,
            $params + [ IcalInterface::EMAIL => 'ildoit1071@example.com' ], // removed, same as value
            $getValue,
            IcalInterface::ORGANIZER .
            ParameterFactory::createParams(
                $params,
                [
                    IcalInterface::CN,
                    IcalInterface::DIR,
                    IcalInterface::SENT_BY,
                    IcalInterface::LANGUAGE
                ]
            ) .
            ':' . $value
        ];

        $value  = 'ildoit1072@example.com';
        $params = [
                strtolower( IcalInterface::CN )           => 'Jane Doe',
                strtolower( IcalInterface::SENT_BY )      => 'boss1072@example.com',
                strtolower( IcalInterface::EMAIL )        => 'MAILTO:another1072@example.com'
            ] + self::$STCPAR;
        $params2 = [
                IcalInterface::CN            => 'Jane Doe',
                IcalInterface::SENT_BY       => 'MAILTO:boss1072@example.com',
                IcalInterface::EMAIL         => 'another1072@example.com'
            ] + self::$STCPAR;
        $getValue  = [
            Util::$LCvalue  => 'MAILTO:' . $value,
            Util::$LCparams => $params2
        ];
        $dataArr[] = [
            1072,
            [
                IcalInterface::ORGANIZER => [ IcalInterface::VEVENT, IcalInterface::VTODO, IcalInterface::VJOURNAL ]
            ],
            $value,
            $params,
            $getValue,
            IcalInterface::ORGANIZER .
            ParameterFactory::createParams(
                $params2,
                [
                    IcalInterface::CN,
                    IcalInterface::DIR,
                    IcalInterface::SENT_BY,
                    IcalInterface::LANGUAGE
                ]
            ) .
            ':' . 'MAILTO:' . $value
        ];

        // CLASS
        $value  = IcalInterface::CONFIDENTIAL;
        $params = self::$STCPAR;
        $getValue  = [
            Util::$LCvalue  => $value,
            Util::$LCparams => $params
        ];
        $dataArr[] = [
            1081,
            [
                IcalInterface::KLASS => [
                    IcalInterface::VEVENT,
                    IcalInterface::VTODO,
                    IcalInterface::VJOURNAL,
                    IcalInterface::VAVAILABILITY
                ]
            ],
            $value,
            $params,
            $getValue,
            IcalInterface::KLASS . ParameterFactory::createParams( $params ) . ':' . $value
        ];

        // STATUS
        $value  = IcalInterface::TENTATIVE;
        $params = self::$STCPAR;
        $getValue  = [
            Util::$LCvalue  => $value,
            Util::$LCparams => $params
        ];
        $dataArr[] = [
            1091,
            [
                IcalInterface::STATUS => [ IcalInterface::VEVENT ]
            ],
            $value,
            $params,
            $getValue,
            IcalInterface::STATUS . ParameterFactory::createParams( $params ) . ':' . $value
        ];

        // STATUS
        $value  = IcalInterface::NEEDS_ACTION;
        $params = self::$STCPAR;
        $getValue  = [
            Util::$LCvalue  => $value,
            Util::$LCparams => $params
        ];
        $dataArr[] = [
            1092,
            [
                IcalInterface::STATUS => [ IcalInterface::VTODO ]
            ],
            $value,
            $params,
            $getValue,
            IcalInterface::STATUS . ParameterFactory::createParams( $params ) . ':' . $value
        ];

        // STATUS
        $value  = IcalInterface::F_NAL;
        $params = self::$STCPAR;
        $getValue  = [
            Util::$LCvalue  => $value,
            Util::$LCparams => $params
        ];
        $dataArr[] = [
            1093,
            [
                IcalInterface::STATUS => [ IcalInterface::VJOURNAL ]
            ],
            $value,
            $params,
            $getValue,
            IcalInterface::STATUS . ParameterFactory::createParams( $params ) . ':' . $value
        ];

        // GEO
        $value  = [ IcalInterface::LATITUDE => 10.10, IcalInterface::LONGITUDE => 10.10 ];
        $params = self::$STCPAR;
        $getValue  = [
            Util::$LCvalue  => $value,
            Util::$LCparams => $params
        ];
        $dataArr[] = [
            1101,
            [
                IcalInterface::GEO => [ IcalInterface::VEVENT, IcalInterface::VTODO ]
            ],
            $value,
            $params,
            $getValue,
            IcalInterface::GEO . ParameterFactory::createParams( $params ) .
            ':' .
            GeoFactory::geo2str2( $getValue[Util::$LCvalue][IcalInterface::LATITUDE], GeoFactory::$geoLatFmt ) .
            Util::$SEMIC .
            GeoFactory::geo2str2( $getValue[Util::$LCvalue][IcalInterface::LONGITUDE], GeoFactory::$geoLongFmt )

        ];

        // COLOR
        $value  = 'black';
        $params = self::$STCPAR;
        $getValue  = [
            Util::$LCvalue  => $value,
            Util::$LCparams => $params
        ];
        $dataArr[] = [
            1103,
            [
                IcalInterface::COLOR => [ IcalInterface::VEVENT, IcalInterface::VTODO, IcalInterface::VJOURNAL ]
            ],
            $value,
            $params,
            $getValue,
            IcalInterface::COLOR . ParameterFactory::createParams( $params ) . ':' . $value
        ];

        // CALENDAR-ADDRESS
        $value  = 'MAILTO:ildoit1071@example.com';
        $params = self::$STCPAR;
        $getValue  = [
            Util::$LCvalue  => $value,
            Util::$LCparams => $params
        ];
        $dataArr[] = [
            1201,
            [
                IcalInterface::CALENDAR_ADDRESS => [ IcalInterface::PARTICIPANT ]
            ],
            $value,
            $params,
            $getValue,
            IcalInterface::CALENDAR_ADDRESS .
            ParameterFactory::createParams( $params ) . ':' . $value
        ];

        // LOCATION-TYPE
        $value  = 'This is a typ of location';
        $params = self::$STCPAR;
        $dataArr[] = [
            1301,
            [
                IcalInterface::LOCATION_TYPE => [ IcalInterface::VLOCATION ]
            ],
            $value,
            $params,
            [
                Util::$LCvalue  => $value,
                Util::$LCparams => $params
            ],
            IcalInterface::LOCATION_TYPE . ParameterFactory::createParams( $params ) . ':' . $value
        ];

        // BUSYTYPE
        $value  = IcalInterface::BUSY_UNAVAILABLE;
        $params = self::$STCPAR;
        $dataArr[] = [
            1401,
            [
                IcalInterface::BUSYTYPE => [ IcalInterface::VAVAILABILITY ]
            ],
            $value,
            $params,
            [
                Util::$LCvalue  => $value,
                Util::$LCparams => $params
            ],
            IcalInterface::BUSYTYPE . ParameterFactory::createParams( $params ) . ':' . $value
        ];

        return $dataArr;
    }

    /**
     * Testing value TEXT (single) properties
     *
     * @test
     * @dataProvider textSingleTest1Provider
     * @param int     $case
     * @param mixed[] $propComps
     * @param mixed   $value
     * @param mixed   $params
     * @param mixed[] $expectedGet
     * @param string  $expectedString
     * @throws Exception
     */
    public function textSingleTest1(
        int    $case,
        array  $propComps,
        mixed  $value,
        mixed  $params,
        array  $expectedGet,
        string $expectedString
    ) : void
    {
        $c = new Vcalendar();
        $urlIsSet = false;
        foreach( $propComps as $propName => $theComps ) {
            if( IcalInterface::SOURCE === $propName ) {
                $c->setSource( $value, $params );
                $c->setUrl( $value, $params );
                continue;
            }
            foreach( $theComps as $theComp ) {
                if( IcalInterface::COLOR === $propName ) {
                    $c->setColor( $value, $params );
                }

                if( ! $urlIsSet && ( IcalInterface::URL === $propName )) {
                    $c->setUrl( $value, $params );
                    $urlIsSet = true;
                }

                $newMethod = 'new' . $theComp;
                switch( true ) {
                    case in_array( $propName, [ IcalInterface::CALENDAR_ADDRESS, IcalInterface::LOCATION_TYPE ], true ) :
                        $vevent = $c->newVevent();
                        $comp  = $vevent->{$newMethod}();
                        break;
                    case ( $theComp === IcalInterface::AVAILABLE ) :
                        $comp = $c->newVavailability()->{$newMethod}();
                        break;
                    default :
                        $comp = $c->{$newMethod}();
                        break;
                }

                $getMethod    = StringFactory::getGetMethodName( $propName );
                $createMethod = StringFactory::getCreateMethodName( $propName );
                $deleteMethod = StringFactory::getDeleteMethodName( $propName );
                $setMethod    = StringFactory::getSetMethodName( $propName );

                if( IcalInterface::GEO === $propName ) {
                    $comp->{$setMethod}( $value[IcalInterface::LATITUDE], $value[IcalInterface::LONGITUDE], $params );
                }
                else {
                    $comp->{$setMethod}( $value, $params );
                }
                if( IcalInterface::CALENDAR_ADDRESS === $propName ) {
                    $vevent->participants2Attendees();
                    $attendee = $vevent->getAttendee();
                    $this->assertEquals(
                        $value,
                        $attendee,
                        sprintf( self::$ERRFMT, null, $case . '-1', __FUNCTION__, $theComp, $getMethod )
                    );
                }
                elseif( IcalInterface::LOCATION_TYPE === $propName ) {  // passive by-pass test
                    $vevent->newParticipant()->{$newMethod}()->{$setMethod}( $value, $params );
                }


                $getValue = $comp->{$getMethod}( true );
                $this->assertEquals(
                    $expectedGet,
                    $getValue,
                    sprintf( self::$ERRFMT, null, $case . '-2', __FUNCTION__, $theComp, $getMethod )
                );

                $createString   = str_replace( self::$EOLCHARS , null, $comp->{$createMethod}() );
                $createString   = str_replace( '\,', ',', $createString );
                $this->assertEquals(
                    $expectedString,
                    trim( $createString ),
                    sprintf( self::$ERRFMT, null, $case . '-3', __FUNCTION__, $theComp, $createMethod )
                );

                $comp->{$deleteMethod}();
                $this->assertFalse(
                    $comp->{$getMethod}(),
                    sprintf( self::$ERRFMT, '(after delete) ', $case . '-4', __FUNCTION__, $theComp, $getMethod )
                );

                if( IcalInterface::GEO === $propName ) {
                    $comp->{$setMethod}( $value[IcalInterface::LATITUDE], $value[IcalInterface::LONGITUDE], $params );
                }
                else {
                    $comp->{$setMethod}( $value, $params );
                }
            } // end foreach
        } // end foreach

        $this->parseCalendarTest( $case, $c, $expectedString );
    }

    /**
     * Test Vevent/Vtodo GEO
     *
     * @test
     */
    public function geoLocationTest4() : void
    {
        $compProps = [
            IcalInterface::VEVENT,
            IcalInterface::VTODO,
        ];
        $calendar  = new Vcalendar();
        $location  = 'Conference Room - F123, Bldg. 002';
        $latitude  = 12.34;
        $longitude = 56.5678;

        foreach( $compProps as $compNames => $theComp  ) {
            $newMethod1 = 'new' . $theComp;
            $comp = $calendar->{$newMethod1}();

            $getValue = $comp->getGeoLocation();
            $this->assertEmpty(
                $getValue,
                sprintf( self::$ERRFMT, null, 1, __FUNCTION__, $theComp, 'getGeoLocation' )
            );

            $comp->setLocation( $location )
                ->setGeo(
                    $latitude,
                    $longitude
                );
            $getValue = explode( '/', $comp->getGeoLocation());
            $this->assertEquals(
                $location,
                $getValue[0],
                sprintf( self::$ERRFMT, null, 2, __FUNCTION__, $theComp, 'getGeoLocation' )
            );
            $tLat = substr( StringFactory::beforeLast('+', $getValue[1] ), 1 );
            $this->assertEquals(
                $latitude,
                $tLat,
                sprintf( self::$ERRFMT, null, 3, __FUNCTION__, $theComp, 'getGeoLocation' )
            );
            $tLong = substr( str_replace( $tLat, null, $getValue[1] ), 1 );
            $this->assertEquals(
                $longitude,
                $tLong,
                sprintf( self::$ERRFMT, null, 4, __FUNCTION__, $theComp, 'getGeoLocation' )
            );
        }
    }
}