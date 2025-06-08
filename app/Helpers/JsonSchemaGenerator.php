<?php

namespace App\Helpers;

class JsonSchemaGenerator
{
    public static function getIcsEventsSchema(): array
    {
        return [
            'name'   => 'ics_events',
            'strict' => true,
            'schema' => self::icsEventSchema(),
        ];
    }

    private static function icsEventSchema(): array
    {
        return [
            'type'       => 'object',
            'properties' => [
                'events' => [
                    'type'  => 'array',
                    'items' => [
                        'type'       => 'object',
                        'properties' => [
                            'starts' => self::dateSchema(),
                            'ends'   => self::dateSchema(),
                            'name'   => [
                                'type' => 'string',
                            ],
                            'description' => [
                                'type' => ['string', 'null'],
                            ],
                            'address' => [
                                'type' => ['string', 'null'],
                            ],
                            'addressName' => [
                                'type' => ['string', 'null'],
                            ],
                            'url' => [
                                'type' => ['string', 'null'],
                            ],
                            'googleConference' => [
                                'type' => ['string', 'null'],
                            ],
                            'microsoftTeams' => [
                                'type' => ['string', 'null'],
                            ],
                            'organizer' => self::personSchema(),
                            'attendees' => [
                                'type'  => ['array', 'null'],
                                'items' => self::personSchema(),
                            ],
                            'isFullDay' => [
                                'type' => ['boolean', 'null'],
                            ],
                            'rrule' => [
                                'type'       => ['object', 'null'],
                                'properties' => [
                                    'frequency' => [
                                        'type' => 'string',
                                    ],
                                    'times' => [
                                        'type' => 'string',
                                    ],
                                    'interval' => [
                                        'type' => 'string',
                                    ],
                                    'starting' => [
                                        'type' => 'string',
                                    ],
                                    'until' => [
                                        'type' => 'string',
                                    ],
                                ],
                                'additionalProperties' => false,
                                'required'             => ['frequency', 'times', 'interval', 'starting', 'until'],
                            ],
                        ],
                        'required'             => ['starts', 'ends', 'name', 'description', 'address', 'addressName', 'url', 'googleConference', 'microsoftTeams', 'organizer', 'attendees', 'isFullDay', 'rrule'],
                        'additionalProperties' => false,
                    ],
                ],
            ],
            'required'             => ['events'],
            'additionalProperties' => false,
        ];
    }

    private static function dateSchema(): array
    {
        return [
            'type'       => 'object',
            'properties' => [
                'at' => [
                    'type' => 'string',
                ],
                'timezone' => [
                    'type' => ['string', 'null'],
                ],
            ],
            'additionalProperties' => false,
            'required'             => ['at', 'timezone'],
        ];
    }

    private static function personSchema(): array
    {
        return [
            'type'       => ['object', 'null'],
            'properties' => [
                'name' => [
                    'type' => ['string', 'null'],
                ],
                'email' => [
                    'type'   => ['string', 'null'],
                    'format' => 'email',
                ],
            ],
            'additionalProperties' => false,
            'required'             => ['name', 'email'],
        ];
    }
}
