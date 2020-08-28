<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class ListsController extends Controller{
    public function lists(Request  $request){
        return view("index.lists");
    }
}
