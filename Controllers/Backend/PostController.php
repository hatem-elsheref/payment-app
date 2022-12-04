<?php

namespace Controllers\Backend;

class PostController{

    public function index()
    {
        return "get All Posts";
    }

    public function show($post_id)
    {
        return "show post with id $post_id";
    }

    public function create()
    {
        return "start create post with showing create form";
    }   

    public function store()
    {
        return "save new post in database";
    }

    public function edit($post_id)
    {
        return "start edit post with id $post_id with showing edit form";
    }

    public function update($post_id)
    {
        return "update post with id $post_id";
    }

    public function destroy($post_id)
    {
        return "delete post with id $post_id";
    }
}