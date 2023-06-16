<?php
/**
 * @OA\Get(path="/bids/{id}", tags={"bids"}, security={{"ApiKeyAuth": {}}}, summary="Return all bids for item from the API.",
 *     @OA\Parameter(in="path", name="id", example=1, description="Id of item"),
 *     @OA\Response(response="200", description="Fetch bids for item of that id")
 * )
 */
Flight::route('GET /bids/@id', function ($id) {
    Flight::json(Flight::bidService()->get_item_bids($id));
});
/**
* @OA\Post(
*     path="/bid",
*     summary="Add bid to an item",
*     tags={"bids"},
*     security={{"ApiKeyAuth": {}}},
*     @OA\RequestBody(description="Basic user info", required=true,
*       @OA\MediaType(mediaType="application/json",
*    			@OA\Schema(
*    				@OA\Property(property="amount", type="integer", example="123",	description="Amount of bid"),
*    				@OA\Property(property="item_id", type="integer", example="15",	description="Item id of the bid" ),
*        )
*     )),
*     @OA\Response(
*         response=200,
*         description="Success bid accepted"
*     ),
*     @OA\Response(
*         response=403,
*         description="Bid too low or item non existing"
*     ),
*     @OA\Response(
*         response=404,
*         description="Auction ended"
*     ),
*     @OA\Response(
*         response=500,
*         description="Bidding on own item"
*     )
* )
*/
Flight::route('POST /bid', function () {
    $request = Flight::request()->data->getData();
    $itemCheck = Flight::itemService()->check_if_ended($request['item_id']);
    if (empty($itemCheck)) {
        Flight::json(["message"=>"Auction ended"], 403);
    } else {
        $highestBid = Flight::bidService()->get_item_bids($request['item_id']);
        if (!empty($highestBid)) {
            if ($request['amount']<=$highestBid[0]['amount']) {
                Flight::json(["message"=>"Bid too low or item id not found"], 404);
            } else {
                Flight::json(Flight::bidService()->add(Flight::get('user'), Flight::request()->data->getData()));
            }
        } else {
            Flight::json(Flight::bidService()->add(Flight::get('user'), Flight::request()->data->getData()));
        }
    }
});
