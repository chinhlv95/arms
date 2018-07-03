<?php

namespace App\Http\Controllers;

use App\Repositories\Tag\TagRepository;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    protected $tagRepository;

    /**
     * ConfigManagerController constructor.
     * @param TagRepository $tagRepository
     */
    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * @author tienhv
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index () {
        $tags = $this->tagRepository->getAll()->toArray();
        return view('config.index',compact('tags'));
    }

    /**
     * @author tienhv
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create (Request $request) {
        $tagName = trim($request->input('tag_name'));
        $query = $this->tagRepository->create(['tag_name' => $tagName]);

        if($query)
        {
            return response()->json($query);
        }
        return false;
    }

    /**
     * @author tienhv
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit (Request $request, $id) {
        
        $tagName = trim($request->input('tag_name'));
        $query = $this->tagRepository->update($id,['tag_name' => $tagName]);

        if($query)
        {
            return response()->json($query);
        }
        return false;
    }

    /**
     * @author tienhv
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete (Request $request, $id) {

        $query = $this->tagRepository->removeTag($id);
        if($query)
        {
            return response()->json($query);
        }
        return false;
    }

    /**
     * @author tienhv
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchTag (Request $request) {
        $result = [
            'code' => 1,
            'data' => $this->tagRepository->search ($request->input('key'))
        ];
        return response()->json($result);
    }

    /**
     * @author theunt
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function existedTag(Request $request){
        $result = [
            'code' => $this->tagRepository->checkIsExistedTag($request->input('name'),$request->input('id')),
        ];
        return response()->json($result);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkUsingTag(Request $request) {
        $result = [
            'code' => $this->tagRepository->checkTagUsing($request->input('id')),
        ];
        return response()->json($result);
    }
}
