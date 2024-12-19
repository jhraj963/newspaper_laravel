<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\News;
use App\Http\Controllers\Api\BaseController;

class NewsController extends BaseController
{
    // Fetch all news
    public function index()
    {
        $data = News::get();
        return $this->sendResponse($data, "News data retrieved successfully");
    }

    // Create a new news item with image upload
    public function store(Request $request)
    {
        $input = $request->all();

        if ($request->hasFile('image')) {
            $imageName = time() . rand(1111, 9999) . '.' . $request->image->extension();
            $imagePath = public_path('/news');
            $request->image->move($imagePath, $imageName);
            $input['image'] = '/news' . $imageName; // Save relative path
        }

        $data = News::create($input);
        return $this->sendResponse($data, "News item created successfully");
    }

    // Show a specific news item
    public function show(News $news)
    {
        return $this->sendResponse($news, "News item retrieved successfully");
    }

    // Update a news item with image upload
    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($news->image && file_exists(public_path($news->image))) {
                unlink(public_path($news->image));
            }

            $imageName = time() . rand(1111, 9999) . '.' . $request->image->extension();
            $imagePath = public_path('/news');
            $request->image->move($imagePath, $imageName);
            $news->image = '/news' . $imageName; // Save relative path
        }

        $news->update($request->except('image')); // Update without image field
        return $this->sendResponse($news, "News item updated successfully");
    }

    // Delete a news item
    public function destroy(News $news)
    {
        if ($news->image && file_exists(public_path($news->image))) {
            unlink(public_path($news->image)); // Delete the image from storage
        }

        $news->delete();
        return $this->sendResponse(null, "News item deleted successfully");
    }
}
