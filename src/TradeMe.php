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

    /**
     * Retrieves detailed information about a single category
     * https://developer.trademe.co.nz/api-reference/catalogue-methods/retrieve-detailed-information-about-a-single-category/
     * @param $categoryId
     * @return mixed
     * @throws \Exception
     */
   public function retrieveSingleCategoryInfo($categoryId){
       $ids = explode('-', $categoryId);
       $ids = array_filter($ids);
       $id = end($ids);
       $url = "Categories/$id/Details.json";
       $response = $this->setMethod('GET')->setHeaders([])->send($url);
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
     * @param Transformer $transformer
     * @param \Closure $callback
     * @return mixed
     * @throws \Exception
     */
   public function createListingByModel($model, Transformer $transformer, $callback=null){
       $response = $this->createListing($transformer->transform($model));
       if ($callback instanceof \Closure){
           $callback($model, $response);
       }
       return $response;
   }

    /**
     * Retrieve listing detail by listing id from trademe
     * https://developer.trademe.co.nz/api-reference/selling-methods/retrieve-the-details-of-a-single-listing/
     * @param $listingId string
     * @return mixed
     * @throws \Exception
     */
   public function retrieveListing($listingId){
       $url = "Selling/Listings/$listingId.json";
       $response = $this->setMethod('GET')->setHeaders([], true)->send($url);
       return $this->convertResponse($response);
   }

    /**
     * update listing
     * @param $product_data array must have ListingId
     * @return mixed
     * @throws \Exception
     */
   public function editListing($product_data){
       $url = "Selling/Edit.json";
       if (!isset($product_data['ListingId'])){
           throw new \Exception('The ID of the listing missing', 404);
       }else{
           $response = $this->setMethod('POST')->setHeaders([], true)
               ->setJson($product_data)->send($url);
           return $this->convertResponse($response);
       }
   }

    /**
     * Relist an item that has expired.
     * https://developer.trademe.co.nz/api-reference/selling-methods/relist-an-item/
     * @param $listingId string Trademe Listing ID
     * @return mixed
     * @throws \Exception
     */
   public function relistItem($listingId){
       $url = 'Selling/Relist.json';
       $response = $this->setMethod('POST')->setHeaders([], true)
           ->setJson([
           'ListingId'=>$listingId,
           'ReturnListingDetails'=>false
            ])
           ->send($url);
       return $this->convertResponse($response);
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

