<?php

class CategoryController extends \BaseController {

	public function get_all_categories(){
        $result = Watchr_category::all();

        return Response::json(
            array(
                "response_msg"=>"Request Ok",
                "data" => $result->toArray())
            ,200);
    }

    /*
     * Returns the main categories with the subcategories
     */
    public function get_all_categories_structured(){

        //get main categories
        $main_categories = Watchr_category::whereNull('fk_subcategory')->get();

        //get subcategories for each of the main categories
        for($i = 0; $i<count($main_categories); $i++){
            $subcategories = $main_categories[$i]->subcategories()->get();
            $main_categories[$i]['subcategories'] = $subcategories->toArray();
        }

        return Response::json(
            array(
                "response_msg"=>"Request Ok",
                "data" => $main_categories->toArray())
            ,200);

    }

    public function get_category_info($category_id){
        $validator = Validator::make(array(
                'category_id' => $category_id
            ),array(
                'category_id' => 'required|integer|exists:watchr_category,category_id'
            ));

        if($validator->fails()){
            return Response::json(array(
                    "error"=>$validator->messages()->all(),
                )
                ,400);
        }

        $result = Watchr_category::find($category_id);
        return Response::json(
            array(
                "response_msg"=>"Request Ok",
                "data" => $result->toArray())
            ,200);
    }
}