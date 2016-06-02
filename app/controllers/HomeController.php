<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/
	public function index()
	{
		$input = Input::all();
		if(!empty($input['q'])){
			$input = Input::all();
			$input['q'] = trim($input['q']); 
			$cache = Cache::get(strtolower($input['q']));
			if(!empty($cache)){
				$result = $cache;
			} else {
				$map_tweet = new MapTweet();
				$result    = $map_tweet->search($input['q'], $input['lat'], $input['lon']);
				Cache::add(strtolower($input['q']), $result, Config::get('constants.CACHE_EXPIRE'));
			}
			
			return $result;
		} else {
			return View::make('index');
		}	
	}

}
