<?php


namespace Hugostech\Trademe;

use GuzzleHttp\Psr7\Response;

class TradeMe extends Base
{
   public function categories($root='Categories', $mcat_path=''){
        $url = "$root.json";
        $response = $this->setQuery(compact('mcat_path'))->send($url);
        return $this->convertResponse($response);
   }

   //to do
   public function _searchPhoto($photo_steam)
   {
        $url = 'Photos/ImageSearch.json';
   }

    /**
     * Adds the photo to the authenticated user’s list of photos. These photos can be used when selling.
     * https://developer.trademe.co.nz/api-reference/photo-methods/upload-a-photo/
     * @param $photo_steam
     * @param $photo_name
     * @param $photo_type
     * @return mixed
     * @throws \Exception
     */
   public function uploadImage($photo_steam, $photo_name, $photo_type){
       $url = 'Photos/Add.json';
       $response = $this->setJson([
           'PhotoData'=>$photo_steam,
           'FileName'=>$photo_name,
           'FileType'=>$photo_type,
       ])->setMethod('POST')->setHeaders([],true)->send($url);
       return $this->convertResponse($response);
   }

   /*
    * Returns a list of all currently available photos uploaded.
    * https://developer.trademe.co.nz/api-reference/photo-methods/retrieve-your-photos/
    */
   public function retrievePhotos(){
       $url = 'Photos.json';
       $response = $this->setHeaders([], true)->send($url);
       return $this->convertResponse($response);
   }

    /**
     * List item to trademe
     * https://developer.trademe.co.nz/api-reference/selling-methods/list-an-item/
     * @param $listing_data
     * @return mixed
     * @throws \Exception
     */
   public function createListing($listing_data){
        $url = 'Selling.json';
        $response = $this->setMethod('POST')->setHeaders([], true)
            ->setJson($listing_data)->send($url);
        return $this->convertResponse($response);
   }

    /**
     * @param $model
     * @param $transformer
     * @param mixed ...$parms
     * @return mixed
     * @throws \Exception
     */
   public function createListingByModel($model, $transformer,...$parms){
       $model_transformer = new $transformer($model, ...$parms);
       return $this->createListing($model_transformer->transform());
   }

   private function convertResponse(Response $response){
       if ($response->getStatusCode()==200){
           return json_decode($response->getBody()->getContents(), true);
       }else{
           throw new \Exception($this->getTradmeErrorMessage($response->getStatusCode()),$response->getStatusCode());
       }
   }

   private function getTradmeErrorMessage($statusCode){
       switch ($statusCode){
           case 200:
               return 'The operation succeeded. Note that many operations return a standard type of response with Success and Description fields. If Success is false then this should be treated the same as if the response was a HTTP 400 error (with the Description field containing the error message).';
           case 304:
               return 'Used with caching to indicate that the cached copy is still valid.';
           case 400:
               return 'The request is believed to be invalid in some way. The response body will contain an error message. You should display the error message to the user.';
           case 401:
               return 'An OAuth authentication failure occurred. You should ask the user to log in again.';
           case 429:
               return 'Your rate limit has been exceeded. Your rate limit will reset at the start of the next hour. You should not attempt to make any more calls until then.';
           case 500:
               return 'A server error occurred. You should display a generic “whoops” error message to the user.';
           default:
               return 'Unkown Error';
       }
   }
}

