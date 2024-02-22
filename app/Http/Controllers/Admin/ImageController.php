<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\User;
use Intervention\Image\ImageManagerStatic as InterImage;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(User::setIjin(20)){
            $images = Image::orderBy('id','desc')
                    ->paginate(15);

                    return view('admin.pages.image')
                            ->with('images', $images);
        }
        return redirect()->route("index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(User::setIjin(20)){
            //
        }
        return redirect()->route("index");

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(User::setIjin(20)){
            if ($request->hasFile('images')) {
                $file = $request->file('images');
                $ext = $file->getClientOriginalExtension();

                $newName = "img-".date('Y-m-d-His')."-".strtolower(str_replace(" ","-",$request->input('name'))) . "." . $ext;

                $image_resize = InterImage::make($file->getRealPath());
                $image_resize->resize(1366, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $image_resize->save(('images/' .$newName));

                $user = Image::create([
                    'description' => $request->input('img_desc'),
                    'name' => $request->input('name'),
                    'keyword' => $request->input('keyword'),
                    'path' => 'images/'.$newName,
                ]);

                if($user){

                    if($request->input('url')=='0'){
                        $resp = array(
                            'status' => '1',
                            'message' => 'success'
                        );

                        return $resp;
                    }else{
                        return redirect($request->input('url'))
                            ->with('status', 1)
                            ->with('message', "Data Tersimpan!");
                    }

                }

            }
        }
        return redirect()->route("index");
    }

    public function storeImageAjax(Request $request)
    {
        if(User::setIjin(20)){
            if ($request->hasFile('images')) {
                $file = $request->file('images');
                $ext = $file->getClientOriginalExtension();

                $newName = "img-".date('Y-m-d-His')."-".strtolower(str_replace(" ","-",$request->input('name'))) . "." . $ext;

                $image_resize = InterImage::make($file->getRealPath());
                $image_resize->resize(1366, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $image_resize->save(('images/' .$newName));

                $user = Image::create([
                    'description' => $request->input('img_desc'),
                    'name' => $request->input('name'),
                    'keyword' => $request->input('keyword'),
                    'path' => 'images/'.$newName,
                ]);

                if($user){

                    if($request->input('url')=='0'){
                        $resp = array(
                            'status' => '1',
                            'message' => 'success'
                        );

                        return $resp;
                    }else{
                        return redirect($request->input('url'))
                            ->with('status', 1)
                            ->with('message', "Data Tersimpan!");
                    }

                }

            }
        }
        return redirect()->route("index");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(User::setIjin(20)){
            //
        }
        return redirect()->route("index");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(User::setIjin(20)){
            $post_images = Image::find($id);
            if (isset($post_images)) {
                return view('admin.pages.image-detail')
                ->with('image', $post_images);
            }else{
                return redirect(url('admin/images'))
                ->with('status', 1)
                ->with('message', "Image not found!");
            }
        }
        return redirect()->route("index");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(User::setIjin(20)){
            $post_images = Image::find($id);
            if (isset($post_images)) {

                $post_images->name = $request->input('name');
                $post_images->keyword = $request->input('keyword');
                $post_images->description = $request->input('img_desc');
                $post_images->save();

            }

            return redirect($request->input('url'))
                ->with('status', 1)
                ->with('message', "Data Updated!");
        }
        return redirect()->route("index");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if(User::setIjin(20)){
            $post_images = Image::find($id);
            if (isset($post_images)) {

                if (file_exists($post_images->path)) {
                    unlink($post_images->path); //menghapus file lama
                }

                $post_images->forceDelete();

            }

            return redirect($request->input('url'))
                ->with('status', 1)
                ->with('message', "Data Deleted!");
        }
        return redirect()->route("index");
    }



    public function getImages()
    {
        if(User::setIjin(20)){
            $images = Image::orderBy('id','desc')
                    ->paginate(15);

                    return view('admin.includes.image-list')
                            ->with('images', $images);
        }
        return redirect()->route("index");
    }
}
