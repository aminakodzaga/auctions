<?php
/**
 * @OA\Get(path="/items", tags={"items"}, security={{"ApiKeyAuth": {}}},
 *         summary="Return all items from the API. ",
 *         @OA\Response( response=200, description="List of items.")
 * )
 */
Flight::route('GET /items', function () {
    Flight::json(Flight::itemService()->get_all_sorted());
});

/**
 * @OA\Get(path="/items/{id}", tags={"items"}, security={{"ApiKeyAuth": {}}}, summary="Return item from the API. ",
 *     @OA\Parameter(in="path", name="id", example=1, description="Id of item"),
 *     @OA\Response(response="200", description="Fetch individual item")
 * )
 */
Flight::route('GET /items/@id', function ($id) {
    Flight::json(Flight::itemService()->get_by_id(Flight::get('user'), $id));
});
/**
 * @OA\Get(path="/useritems", tags={"items"}, security={{"ApiKeyAuth": {}}},
 *         summary="Return all user items from the API. ",
 *         @OA\Response( response=200, description="List of items.")
 * )
 */
Flight::route('GET /useritems', function () {
    Flight::json(Flight::itemService()->get_user_items(Flight::get('user')));
});
/**
* @OA\Post(
*     path="/item",
*     description="Add item to auction",
*     tags={"items"},
*     security={{"ApiKeyAuth": {}}},
*     summary="Adds item to auction. ",
*     @OA\RequestBody(
*         @OA\MediaType(
*             mediaType="multipart/form-data",
*             @OA\Schema(
*                 allOf={
*                     @OA\Schema(
*                         @OA\Property(property="title", type="string", example="Test",	description="Title of item"),
*                         @OA\Property(property="description", type="string", example="test",	description="Desc of item"),
*                         @OA\Property(
*                             description="Item image",
*                             property="item_image",
*                             type="string", format="binary"
*                         ),
*    				               @OA\Property(format="datetime", property="ending", description="Ending at.", example="2022-05-18 07:50:45"),
*                     )
*                 }
*             )
*         )
*     ),
*     @OA\Response(
*         response=200,
*         description="Success user registered"
*     )
* )
*/
Flight::route('POST /item', function () {
    $request = Flight::request();
    $file = $request->files['imageInput'];
    $target_dir = "../img/items/";
    $target_file = $file['name'];
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    // Check if image file is a actual image or fake image
    $check = getimagesize($file["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $uploadOk = 0;
    }

    // Check file size
    if ($file["size"] > 500000) {
        Flight::json(["message" => "Your image file is too large"], 500);
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif") {
        Flight::json(["message" => "Image format not allowed"], 500);
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        return;
        // if everything is ok, try to upload file
    } else {
        $imagePath = generateRandomString(19).".".$imageFileType;
        if (move_uploaded_file($file["tmp_name"], $target_dir.$imagePath)) {
        } else {
            Flight::json(["message" => "Sorry, there was an error uploading your file."], 500);
            return;
        }
    }
    $entity = $request->data->getData();
    $entity['image'] = $imagePath;
    Flight::json(Flight::itemService()->add(Flight::get('user'), $entity));
});
function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
/**
* @OA\Delete(
*     path="/item/{id}", security={{"ApiKeyAuth": {}}},summary="Delete item from the API. ",
*     description="Delete item and its bids from API",
*     tags={"items"},
*     @OA\Parameter(in="path", name="id", example=1, description="Item ID"),
*     @OA\Response(
*         response=200,
*         description="Item deleted"
*     ),
*     @OA\Response(
*         response=500,
*         description="Error"
*     )
* )
*/
Flight::route('DELETE /item/@id', function ($id) {
    Flight::bidService()->delete(Flight::get('user'), $id);
    Flight::itemService()->delete(Flight::get('user'), $id);
    Flight::json(["message" => "deleted"]);
});
