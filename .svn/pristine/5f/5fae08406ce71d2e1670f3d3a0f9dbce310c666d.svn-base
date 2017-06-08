<?php

namespace App\Api\V1\Controllers\Article;
use App\Api\V1\Controllers\BaseController;
use App\Api\V1\Transformers\OrderTransformer;

use App\Models\Carticle;
use App\Serializer\CustomSerializer;
use Illuminate\Http\Request;
use App\Models\Order;
use Validator;
use Swagger\Annotations as SWG;


class ArticleController extends BaseController {

    /**
     * @SWG\Get(
     * path="/ordr/all",
     * summary="获取公司下所有审核通过并分配了的项目",
     * tags={"Projects"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Response(
     *   response=401,
     *   description="token过期"
     * ),
     * @SWG\Response(
     *   response=400,
     *   description="token无效"
     * ),
     * @SWG\Response(
     *   response=404,
     *   description="用户没找到"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="获取成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="获取失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function show(Request $request) {

        $validator = Validator::make($request->all(), [
            'page'=>"required|integer",
            'page_size'=>"required|integer",
        ]);
        if($validator->fails())
        {
            return $this->errorResponse($validator->errors()->first());
        }
        $list = Carticle::getAll($request->all());
        return $this->successResponse($list);
    }

    public function one(Request $request) {

        $validator = Validator::make($request->all(), [
            'id'=>"required|integer",
        ]);
        if($validator->fails())
        {
            return $this->errorResponse($validator->errors()->first());
        }
        $list = Carticle::find($request->id)->toArray();
        return $this->successResponse($list);
    }
    
    public function delete(Request $request)
    {
        $all = $request->all();
        Carticle::deleteArticle($all);
        return $this->successResponse([]);
    }
    
    public function saveArticle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'=>"required",
            'contents'=>"required",
            'status'=>"required",
            'id'=>"integer",
        ]);
        if($validator->fails())
        {
            return $this->errorResponse($validator->errors()->first());
        }
        $article = new Carticle();
        if(isset($request->id))
        {
            $article = $article->where("id",'=',$request->id)->first();
        }
        $article->title = $request->title;
        $article->content = $request->contents;
        $article->status = $request->status;
        $article->save();
        return $this->successResponse();
    }

}
