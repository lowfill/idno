<?php

    namespace IdnoPlugins\Checkin {

        class Checkin extends \Idno\Common\Entity
        {

            function getTitle()
            {
                return 'Checked into ' . $this->placename;
            }

            function getDescription()
            {
                if (empty($this->body)) {
                    return ' ';
                }
            }

            /**
             * Status objects have type 'note'
             * @return 'note'
             */
            function getActivityStreamsObjectType()
            {
                return 'place';
            }

            /**
             * Saves changes to this object based on user input
             * @return true|false
             */
            function saveDataFromInput()
            {

                if (empty($this->_id)) {
                    $new = true;
                } else {
                    $new = false;
                }
                $body         = \Idno\Core\site()->currentPage()->getInput('body');
                $lat          = \Idno\Core\site()->currentPage()->getInput('lat');
                $long         = \Idno\Core\site()->currentPage()->getInput('long');
                $user_address = \Idno\Core\site()->currentPage()->getInput('user_address');
                $placename    = \Idno\Core\site()->currentPage()->getInput('placename');

                if (!empty($lat) && !empty($long)) {
                    $this->lat       = $lat;
                    $this->long      = $long;
                    $this->placename = $placename;
                    $this->title     = 'Checked into ' . $placename;
                    $this->body      = $body;
                    $this->address   = $user_address;
                    $this->setAccess('PUBLIC');
                    if ($this->save()) {
                        if ($new) {
                            $this->addToFeed();
                            \Idno\Core\Webmention::pingMentions($this->getURL(), \Idno\Core\site()->template()->parseURLs($this->getDescription()));
                        }

                        return true;
                    }
                } else {
                    \Idno\Core\site()->session()->addMessage('You can\'t save an empty checkin.');
                }

                return false;

            }

            function deleteData()
            {
                \Idno\Core\Webmention::pingMentions($this->getURL(), \Idno\Core\site()->template()->parseURLs($this->getDescription()));
            }

            /**
             * Given a latitude and longitude, reverse geocodes it into a structure including name, address,
             * city, etc
             *
             * @param $latitude
             * @param $longitude
             * @return bool|mixed
             */
            static function queryLatLong($latitude, $longitude)
            {

                $query    = self::getNominatimEndpoint() . "reverse?lat={$latitude}&lon={$longitude}&format=json&zoom=18";
                $response = [];

                $http_response = \Idno\Core\Webservice::get($query)['content'];

                if (!empty($http_response)) {
                    if ($contents = @json_decode($http_response)) {
                        if (!empty($contents->address)) {
                            $addr             = (array)$contents->address;
                            $response['name'] = implode(', ', array_slice($addr, 0, 1));
                        }
                        if (!empty($contents->display_name)) {
                            $response['display_name'] = $contents->display_name;
                        }

                        return $response;
                    }
                }

                return false;

            }

            /**
             * Takes an address and returns OpenStreetMap data via Nominatim, including latitude and longitude
             *
             * @param string $address
             * @return array|bool
             */
            static function queryAddress($address, $limit = 1)
            {

                $query = self::getNominatimEndpoint() . "search?q=" . urlencode($address) . "&format=json";

                $http_response = \Idno\Core\Webservice::get($query)['content'];

                if (!empty($http_response)) {
                    if ($contents = @json_decode($http_response)) {
                        $contents              = (array)$contents;
                        $contents              = (array)array_pop($contents); // This will have been an array wrapped in an array
                        $contents['latitude']  = $contents['lat'];
                        $contents['longitude'] = $contents['lon'];

                        return $contents;
                    }
                }

                return false;

            }

            /**
             * Returns the OpenStreetMap Nominatim endpoint that we should be using
             * @return string
             */
            static function getNominatimEndpoint()
            {
                if ($config = \Idno\Core\site()->config()->checkin) {
                    if (!empty($config['endpoint'])) {
                        return $config['endpoint'];
                    }
                }

                return 'http://nominatim.openstreetmap.org/';
            }

        }

    }