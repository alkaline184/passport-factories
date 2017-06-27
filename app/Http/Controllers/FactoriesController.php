<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use LRedis;
class FactoriesController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Factories Controller
	|--------------------------------------------------------------------------
	|
	| This controller serves as a main entry point for the Passport Factories application
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest');
	}

	/**
	 * Show the application factories screen
	 *
	 * @return Response
	 */
	public function index()
	{
		$client = new \GuzzleHttp\Client();

		//Check for errors
		try {
	    	$res = $client->get(config('app.factory_api_url') . "factories/");
		}
		catch (\GuzzleHttp\Exception\ConnectException $e) {
			Log::error($e->getMessage());
			return view('alert', ['factories'=> '', 'error'=>'Can\'t connect to API resource.']);
		} catch (\Exception $e){
			Log::error($e->getMessage());
			return view('alert', ['factories'=> '', 'error'=>'Something went wrong']);
		}

	    return view('factories', ['factories'=> json_decode($res->getBody())]);
	}

	public function getFactory($id)
	{

		$client = new \GuzzleHttp\Client();
		try {
	    	$res = $client->get(config('app.factory_api_url') . "factories/$id");
		} catch (\Exception $e){
			Log::error($e->getMessage());
			abort(500, 'Error while fetching factory');
		}

	    return $res->getBody();
	}

	public function getFactories()
	{
		$client = new \GuzzleHttp\Client();
		try {
	    	$res = $client->get(config('app.factory_api_url') . "factories/");
		} catch (\GuzzleHttp\Exception\ConnectException $e) {
			Log::error($e->getMessage());
			return view('alert', ['factories'=> '', 'error'=>'Can\'t connect to API resource.']);
		} catch (\Exception $e){
			Log::error($e->getMessage());
			return view('alert', ['factories'=> '', 'error'=>'Something went wrong']);
		}

		return view('factory_data', ['factories'=> json_decode($res->getBody())]);

	}

	public function deleteFactory($id)
	{

		$client = new \GuzzleHttp\Client();

		try {
	    	$res = $client->delete(config('app.factory_api_url') . "factories/$id");
	    } catch (\Exception $e){
			Log::error($e->getMessage());
			abort(500, 'Error while deleting factory');
		}
	    //Push the message to Redis
	   	$redis = LRedis::connection();
		$redis->publish('factories', "Factories Updated");

	    echo $res->getBody();
	}

	public function createFactory(Request $request)
	{
		
		$body['name'] =  $request->input('name');
		$body['lower'] = $request->input('lower');
		$body['upper'] = $request->input('upper');
		$body['count'] = $request->input('count');

		$client = new \GuzzleHttp\Client();

		try {
	    	$res = $client->post(config('app.factory_api_url') . "factories/", ['body'=>$body]);
	    } catch (\Exception $e){
			Log::error($e->getMessage());
			abort(500, 'Error while creating factory');
		}

	     //Push the message to Redis
	   	$redis = LRedis::connection();
		$redis->publish('factories', "Factories Updated");

	    //echo $res->getStatusCode();

	    echo $res->getBody();
	}

	public function updateFactory(Request $request, $id)
	{
		$body['name'] =  $request->input('name');
		$body['lower'] = $request->input('lower');
		$body['upper'] = $request->input('upper');
		$body['count'] = $request->input('count');

		$client = new \GuzzleHttp\Client();

		try {
	    	$res = $client->put(config('app.factory_api_url') . "factories/$id", ['body'=>$body]);
	    } catch (\Exception $e){
			Log::error($e->getMessage());
			abort(500, 'Error while updating factory');
		}
	     //Push the message to Redis
	   	$redis = LRedis::connection();

		$redis->publish('factories', "Factories Updated");

	    echo $res->getStatusCode();

	    echo $res->getBody();
	}

	public function addChildren($id)
	{

		$client = new \GuzzleHttp\Client();

		try {
	   	 	$res = $client->put(config('app.factory_api_url') . "factories/$id/children");
	    } catch (\Exception $e){
			Log::error($e->getMessage());
			abort(500, 'Error while adding children to the factory');
		}

	     //Push the message to Redis
	   	$redis = LRedis::connection();
		$redis->publish('factories', "Factories Updated");

	    echo $res->getBody();
	}

}
