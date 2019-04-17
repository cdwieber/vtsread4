<?php
/**
 * Created by PhpStorm.
 * User: chriswieber
 * Date: 2/26/18
 * Time: 1:08 PM
 */

namespace VTS;
use KamranAhmed\Geocode;

//TODO: This class should be further broken down, it's too big and varied in responsibility. _S_OLID

class VTS_Import {

	private $api_key;
	private $api_secret;
	private $api_url;
	private $log;

	/**
	 * VTS_Import constructor.
	 */
	public function __construct() {
		$this->api_url = "https://api.vts.com/api/marketing/v1/properties";
		$this->api_key = get_option('api_key');
		$this->api_secret = get_option('api_secret');

		$this->log = $log = new VTSRead_Log();

		//Add the cron hook for imports.
//		add_action( 'vts_cron_hook', array($this, 'vtsread_import_cron') );
//		if ( ! wp_next_scheduled( 'vts_cron_hook' ) ) {
//			wp_schedule_event( time(), 'hourly', 'vts_cron_hook' );
//		}
	}

	public function vts_import_cron() {
		$this->log->write("********~CRON INITIATED IMPORT~*******");
		$this->vtsread_import();
		// This is just for my initial verifications and will be removed in production versions, deferring to logs.
        // And of course, if you're reading this, the code is in production and I didn't because that's how it be sometimes.
		wp_mail('chris.wieber@gmail.com','Cron Fired',"The cron fired. Yaaaaay.");
	}

	/**
	 * Primary method responsible for batch processing and importing listings.
	 * @return bool
	 */
	public function vtsread_import() {
		set_time_limit(0);
		//Clear any existing entries from the temp log
		$this->log->clear_tmp();

		$this->log->write("********~Starting new import~********");

		$props = $this->getAllProperties();

		$index = 1;
		$count = count($props);

		$this->log->write("Found $count properties to import.");
		foreach($props as $prop) {

			$address_string = $prop['street_address'].", ".$prop['city']['name'].", ".$prop['state'].", ".$prop['zip_code'];

			$location = $this->geocode($address_string);

			$name = strstr($prop['name'], '-',true);//Trim the internal market codes off title
			$internal_code = substr($prop['name'], (strpos($prop['name'], '-')+2));

			$data = [
				'id'            => $prop['id'],
				'street_address'=> $prop['street_address'],
				'city'          => $prop['city']['name'],
				'state'         => $prop['state'],
				'zip'           => $prop['zip_code'],
				'name'          => $name,
				'internal_code' => $internal_code,
				'space_available'=>$prop['space_available'],
				'building_class'=> $prop['building_class'],
				'photo_url'     => $prop['photos'][0]['url'], //VTS only has one, yet it's buried in a multidimensional array. Whatevs, HAX!
				'space_total'   => $prop['occupied_square_feet'] + $prop['space_available'],
				'year_built'    => $prop['year_built'],
				'number_of_floors'=>$prop['number_of_floors'],
				'description' => $prop['description'],
				'post_content' => " ",
				'lat' => $location->getLatitude(),
				'long' => $location->getLongitude(),
			];



			$this->update_custom_post($data);

			$this->log->write("Imported {$prop['name']}. ($index of $count)");
			$index++;
		}
		//Kill the process here since it's AJAX and we don't want it running amok thinking it can do whatever it wants
		error_log("Refresh complete");
		//Flush permalinks
        flush_rewrite_rules();
		$this->log->write("********Refresh completed.********");

		wp_die();

		return true;
	}

	/**
	 * Get a single 'page' of listings from VTS.
	 * @param string $page
	 *
	 * @return mixed
	 */
	public function get($page = '')
	{
		//set page to get
		if ($page != '' && is_int($page)) {
			$this->api_url .= '?page='.$page;
		}

		//initialize curl
		$curl = curl_init($this->api_url);

		//curl config
		$options = [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_USERPWD => $this->api_key.":".$this->api_secret,
			CURLOPT_SSL_VERIFYPEER => FALSE
		];
		curl_setopt_array($curl, $options);

		$result = curl_exec($curl);

		if($errno = curl_errno($curl)) {
			$error_message = curl_strerror($errno);
			error_log("cURL error ({$errno}):\n {$error_message}");
		}

		//Destroy the API url so it gets refreshed on subsequent calls. DESTROY IT, ISILDOR. THROW IT INTO THE FIRE.
		//Then set it back because we need to reset it again on subsequent runs
		//TODO: PAY OFF THIS TECHNICAL DEBT
		unset($this->api_url);
		$this->api_url = "https://api.vts.com/api/marketing/v1/properties";
		return $result;
	}

	/**
	 * Return a single listing by ID as array.
	 * @param $id
	 *
	 * @return array|mixed|object
	 */
	public function getById($id) {

		$this->api_url .= "/".$id;

		$curl = curl_init($this->api_url);

		//curl config
		$options = [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_USERPWD => $this->api_key.":".$this->api_secret,
		];
		curl_setopt_array($curl, $options);

		$result = curl_exec($curl);

		return json_decode($result, true);
	}

	/**
	 * Iterates through all pages of properties and returns array of all listings,
	 * top level representing individual listings.
	 * @return array
	 */
	public function getAllProperties()
	{
		//Get the first page so we can retrieve the page number
		$result = json_decode($this->get());

		$all_pages = $result->total_pages;

		$this->log->write("Found $all_pages pages of properties to import.");

		//Loop through all available pages and suck out all the delicious, delicious data
		$all = [];
		for($current_page = 1; $current_page <= $all_pages; $current_page++)
		{
			$prop_page = json_decode($this->get($current_page), true);
			//Access the nested array
			foreach($prop_page['properties'] as $property) {
				array_push($all, $property);
			}
			$this->log->write("Processed page $current_page of $all_pages.");
		}
		return $all;
	}

	/**
	 * Geocodes human-readable address string to lat-long coordinates.
	 * If the initial geocode fails, method will attempt the same call
	 * three times before returning false and moving skipping it.
	 *
	 * The first couple of times may be due to poor connection or API timeout,
	 * but after three it's usually because of an improperly formatted user-supplied
	 * address, so we won't wait around for it too long.
	 *
	 * @param $address_string
	 *
	 * @return bool|object
	 */
	private function geocode($address_string) {

		$geocode = new Geocode\Geocode(get_option('google_maps_key'));
		$location = $geocode->get($address_string);

		//If the geocode operation fails, try again three times before giving up.
		if($location->getLatitude() == 0  || $location->getLongitude() == 0) {

			$this->log->write("Property failed to geocode! Address provided: " . $address_string);
			error_log( "Property failed to geocode! Address provided: " . $address_string );

			for ( $i = 1; $i <= 3; $i ++ ) {

				error_log( "Attempting again... ({$i} of 3)" );
				$this->log->write( "Attempting again... ({$i} of 3)" );

				$location = $geocode->get( $address_string );

				if ( $location->getLatitude() > 0 || $location->getLongitude() < 0 ) {
					error_log( "({$location->getLatitude()} {$location->getLongitude()}) -- Geocode Successful, moving on..." );
					$this->log->write("({$location->getLatitude()} {$location->getLongitude()}) -- Geocode Successful, moving on...");
					break;
				}

				if ( $i == 3 ) {
					error_log( "Geocode failed after 3 attempts." );
					$this->log->write("Geocode failed after 3 attempts." );
				}
			}
		}
		return $location;
	}

	/**
	 * Updates the 'listing' custom post. If not found by ID, creates one.
	 * @param $data
	 */
	private function update_custom_post( $data ) {

		//Is there a post for the ID provided?
		if ( get_post_status( $data['id'] ) ) {
			$post    = array(
				'ID'             => $data['id'],
				'comment_status' => 'closed',
				'post_content'   => isset( $data['description'] ) ? $data['description'] : "",
				'post_name'      => $data['name'],
				'post_status'    => 'publish',
				'post_title'     => $data['name'],
				'post_type'      => 'listing',
			);
			$post_id = wp_insert_post( $post );


		} else {
			//If not, create one with the same ID that's in VTS
			$post = array(
				'import_id'      => $data['id'],
				'comment_status' => 'closed',
				'post_content'   => isset( $data['description'] ) ? $data['description'] : "",
				'post_name'      => $data['name'],
				'post_status'    => 'publish',
				'post_title'     => $data['name'],
				'post_type'      => 'listing',
			);

			$post_id = wp_insert_post( $post );
		}
			//ACF specific methods to update the custom fields with data from VTS
			update_field( 'field_5a41889d49a00', $data['street_address'], $data['id'] );
			update_field( 'field_5a4187ab499fe', $data['space_available'], $data['id'] );
			update_field( 'field_5a418861499ff', $data['space_total'], $data['id'] );
			update_field( 'field_5a4188c549a01', $data['city'], $data['id'] );
			update_field( 'field_5a4188cd49a02', $data['state'], $data['id'] );
			update_field( 'field_5a4188d149a03', $data['zip'], $data['id'] );
			update_field( 'field_5a4188f749a05', $data['year_built'], $data['id'] );
			update_field( 'field_5a41891649a06', $data['number_of_floors'], $data['id'] );
			update_field( 'field_5a41a94a0871a', $data['lat'], $data['id'] );
			update_field( 'field_5a41a95f0871b', $data['long'], $data['id'] );
			update_field( 'field_5a7209a900412', $data['internal_code'], $data['id'] );
			update_field( 'field_5a73c52dcabc4', $data['name'] . " " . $data['street_address'], $data['id'] );


	}

}