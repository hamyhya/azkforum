<?php

namespace App\Http\Controllers;

use App\forum;
use Illuminate\Http\Request;
use Auth;
use App\Tag;
use Storage;

class ForumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth')->except('index', 'show');
    }


    public function index()
    {
        $forums = Forum::orderBy('id', 'DESC')
            ->paginate(5);
        return view('forum.index', compact('forums'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        $forums = Forum::orderBy('id', 'desc')->paginate(1);
        $tags = Tag::all();
        return view('forum.create', compact('tags', 'forums'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'tags' => 'required',
            'image' => 'image|mimes:jpg,jpeg,png,gif|max:2048',

        ]);
        $forums = new Forum;
        $forums->user_id = Auth::user()->id;
        $forums->title = $request->title;
        $forums->slug = str_slug($request->title);
        $forums->description = $request->description;
        if ($request->file('image')) {
               $file = $request->file('image');
               $filename = time().'.'.$file->getClientOriginalExtension();
               $location = public_path('/images');
               $file->move($location, $filename);
               $forums->image = $filename;

           }
           $forums->save(); 
           $forums->tags()->sync($request->tags);

           return back()->withInfo('Yay! Pertanyaan berhasil diupload');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\forum  $forum
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $shows = Forum::all();
        $forums = Forum::where('id', $slug)
                        ->orWhere('slug', $slug)
                        ->firstOrFail();

        return view('forum.show', compact('forums', 'shows'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\forum  $forum
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        
        $tags = Tag::all();
            $forum = Forum::where('id', $slug)
                ->orWhere('slug', $slug)
                ->firstOrFail();
        return view('forum.edit', compact('forum', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\forum  $forum
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'tags' => 'required',
            'image' => 'image|mimes:jpg,jpeg,png,gif|max:2048',

        ]);

        $forums = Forum::find($id);

        $forums->user_id = Auth::user()->id;
        $forums->title = $request->title;
        $forums->slug = str_slug($request->title);
        $forums->description = $request->description;
        if ($request->file('image')) {
               $file = $request->file('image');
               $filename = time().'.'.$file->getClientOriginalExtension();
               $location = public_path('/images');
               $file->move($location, $filename);
               
               $oldImage = $forums->image;
               \Storage::delete($oldImage);

               $forums->image = $filename;

           }
           $forums->save();
           $forums->tags()->sync($request->tags);
           
           return redirect('forum')->withInfo('Sip! Pertanyaan telah diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\forum  $forum
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $forum = Forum::find($id);
        Storage::delete($forum->image);
        $forum->tags()->detach();
        $forum->delete();
        return back()->withInfo('Pertanyaan berhasil dihapus!');
    }
}
